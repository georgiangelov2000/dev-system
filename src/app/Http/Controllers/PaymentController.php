<?php

namespace App\Http\Controllers;

use App\Helpers\FunctionsHelper;
use Exception;
use Illuminate\Http\Request;
use App\Http\Requests\PaymentRequest;
use App\Models\OrderPayment;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View; // Import the View class

class PaymentController extends Controller
{
    private $paymentMethods;
    private $helper;
    private $paymentService;
    private $paymentStatuses;

    /**
     * Constructor for the PaymentController.
     *
     * @param PaymentService $paymentService - The payment service for handling payments.
     */
    public function __construct(PaymentService $paymentService, FunctionsHelper $helper)
    {
        $this->paymentService = $paymentService;
        $this->paymentMethods  = config('statuses.payment_methods_statuses');
        $this->paymentStatuses = config('statuses.payment_statuses');
        $this->helper = $helper;
    }

    /**
     * Calls paymentService to redirect to the respective view page.
     *
     * @param string $type - Type of payment ('order' or 'purchase').
     * @return View|null - Returns a view or null if the type is not in the list.
     */
    public function index($type): ?View
    {
        return $this->paymentService->getData($type);
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
        return $this->paymentService->getData($type, $payment);
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
     * Processes payment data and saves it to the database.
     *
     * @param array $data - Array of payment data.
     * @param mixed $builder - Payment model instance.
     * @param string $relation - Related model ('order' or 'purchase').
     */
    private function paymentProcessing(array $data, $builder, $relation)
    {
        // Validate payment status and method
        $validPaymentStatus = $this->helper->statusValidation($data['payment_status'], $this->paymentStatuses);
        $validPaymentMethod = $data['payment_method'] && $this->helper->statusValidation($data['payment_method'], $this->paymentMethods);

        // Set payment status if valid
        if ($validPaymentStatus) {
            $builder->payment_status = $data['payment_status'];
        }

        // Set payment method if valid
        if ($validPaymentMethod) {
            $builder->payment_method = $data['payment_method'];
        }

        // Fill payment attributes from input data
        $builder->fill([
            'price' => $data['price'],
            'quantity' => $data['quantity'],
            'date_of_payment' => now()->parse($data['date_of_payment']),
            'payment_reference' => $data['payment_reference'],
            'partially_paid_price' => $data['partially_paid_price'] ?? '0.00', // Set to '0.00' if not provided
        ])->save();

        // Update invoice attributes
        $builder->invoice()->update([
            'price' => $builder->price,
            'quantity' => $builder->quantity
        ]);
    }
}
