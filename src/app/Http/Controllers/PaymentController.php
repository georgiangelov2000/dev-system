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
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
    
            if (!$builder) {
                throw new ModelNotFoundException("Payment not found");
            }
    
            $relation = $builder instanceof OrderPayment ? 'order' : 'purchase';
            $builder->load($relation);
    
            $data = $request->validated();
            $this->paymentProcessing($data, $builder, $relation);
    
            DB::commit();
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return abort(404);
        } catch (Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Payment has not been updated');
        }
    
        return redirect()->back()->with('success', 'Payment has been updated');
    }

    /**ce->getInstance($payment,
     * Processes payment data and saves it to the database.
     *
     * @param array $data - Array of payment data.
     * @param mixed $builder - Payment model instance.
     * @param string $relation - Related model ('order' or 'purchase').
     */
    private function paymentProcessing(array $data, $builder, $relation)
    {   
        $validPaymentMethod = $data['payment_method'] && $this->helper->statusValidation($data['payment_method'], $this->paymentMethods);
        
        // Check if the payment method is not valid and throw an exception
        if (!$validPaymentMethod) {
            throw new \Exception('Invalid payment method.');
        }
        $delivery_date = null;
        
        // Validate payment status and method
        if ($data['is_it_delivered']) {
            $expected = $builder->expected_date_of_payment;
            $dateOfPayment = $data['date_of_payment'];
            $delivery_date = now()->parse($data['delivery_date']);

            if ($dateOfPayment >= $expected) {
                $status = $builder::PAID;
            } elseif ($dateOfPayment <= $expected) {
                $status = $builder::OVERDUE;
            }
    
            $builder->payment_status = $status;
            $builder->date_of_payment = now()->parse($dateOfPayment);
            $builder->$relation->is_it_delivered = $builder->$relation::IS_IT_DELIVERED_TRUE;
            $builder->$relation->delivery_date = $delivery_date;

            $builder->$relation->save();
        }
        
        $builder->price = $data['price'];
        $builder->quantity = $data['quantity'];
        $builder->payment_reference = $data['payment_reference'];
        $builder->partially_paid_price = $data['partially_paid_price'] ?? '0.00';
        $builder->payment_method = $data['payment_method'];

        $builder->save();
    
        // Update invoice attributes
        $builder->invoice()->update([
            'price' => $builder->price,
            'quantity' => $builder->quantity
        ]);
    }
}
