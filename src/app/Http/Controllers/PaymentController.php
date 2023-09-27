<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Http\Requests\PaymentRequest;
use App\Models\OrderPayment;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View; // Import the View class

class PaymentController extends Controller
{
    private $paymentStatuses;

    private $paymentMethods;

    private $paymentService;

    const DEFAULT_STATUS = 6;

    /**
     * Constructor for the PaymentController.
     *
     * @param PaymentService $paymentService - The payment service for handling payments.
     */
    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
        $this->paymentStatuses = config('statuses.payment_statuses');
        $this->paymentMethods  = config('statuses.payment_methods_statuses');
    }

    /**
     * Calls paymentService to redirect to the respective view page.
     *
     * @param string $type - Type of payment ('order' or 'purchase').
     * @return View|null - Returns a view or null if the type is not in the list.
     */
    public function index($type): ?View
    {
        return $this->paymentService->redirectToView($type);
    }

    /**
     * Calls paymentService to redirect to the payment edit view.
     *
     * @param int $payment - Payment ID.
     * @param string $type - Type of payment ('order' or 'purchase').
     * @return View|null - Returns a view or null if the type is not in the list.
     */
    public function edit($payment, $type)
    {
        return $this->paymentService->redirectToView($type, $payment);
    }

    /**
     * Updates payment information after data validation.
     *
     * @param PaymentRequest $request - Payment request.
     * @param int $payment - Payment ID.
     * @param string $type - Type of payment ('order' or 'purchase').
     * @return \Illuminate\Http\RedirectResponse - Redirects back to the previous page with a success or error message.
     */
    public function update(PaymentRequest $request, $payment, $type)
    {
        DB::beginTransaction();

        try {
            $builder = $this->paymentService->getInstance($payment, $type);
            $relation = $builder instanceof OrderPayment ? 'order' : 'purchase';
            $data = $request->validated();
            $this->paymentProcessing($data, $builder, $relation);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Payment has not been updated');
        }

        return redirect()->back()->with('success', 'Payment has been updated');
    }

    /**
     * Deletes the payment and, if the related model is available, sets its status to the default value.
     *
     * @param int $payment - Payment ID.
     * @param string $type - Type of payment ('order' or 'purchase').
     * @return \Illuminate\Http\JsonResponse - JSON response with status 200, 404, or 500 depending on the outcome.
     */
    public function delete($payment, $type)
    {
        DB::beginTransaction();

        try {
            $builder = $this->paymentService->getInstance($payment, $type);

            $relatedModel = $builder->{$type};

            if ($relatedModel) {
                $relatedModel->status = self::DEFAULT_STATUS;
                $relatedModel->save();
            }

            // Delete the payment
            $builder->delete();
            DB::commit();

            if ($relatedModel) {
                return response()->json(['message' => 'Payment has been deleted'], 200);
            } else {
                return response()->json(['message' => $type . ': Not found'], 404);
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Payment could not be deleted'], 500);
        }
    }


    /**
     * Processes payment data and saves it to the database.
     *
     * @param array $data - Array of payment data.
     * @param mixed $builder - Payment model instance.
     * @param string $relation - Related model ('order' or 'purchase').
     */
    private function paymentProcessing(array $data, $builder, $relation)
    {
        $relation = $builder->$relation;

        if (isset($data['payment_status']) && array_key_exists($data['payment_status'], $this->paymentStatuses)) {
            $relation->status = $data['payment_status'];
            $builder->payment_status = $data['payment_status'];
            $relation->save();
        }

        if (isset($data['payment_method']) && array_key_exists($data['payment_method'], $this->paymentMethods)) {
            $builder->payment_method = $data['payment_method'];
        }

        $builder->price = $data['price'];
        $builder->quantity = $data['quantity'];
        $builder->date_of_payment = now()->parse($data['date_of_payment']);
        $builder->payment_reference = $data['payment_reference'];
        $builder->save();

        $builder->invoice()->update([
            'price' => $builder->price,
            'quantity' => $builder->quantity
        ]);
    }
}
