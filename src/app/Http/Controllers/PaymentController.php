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
use App\Factory\Payments\PaymentViewFactory;

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
    public function __construct(FunctionsHelper $helper)
    {
        $this->paymentMethods  = config('statuses.payment_methods_statuses');
        $this->paymentStatuses = config('statuses.payment_statuses');
        $this->helper = $helper;
    }

    /**
     * Calls PaymentViewFactory to redirect to the respective view page.
     *
     * @param string $type - Type of payment ('order' or 'purchase').
     */
    public function index($type): ?View
    {
        return PaymentViewFactory::select($type);
    }

    /**
     * Calls PaymentViewFactory to redirect to the payment edit view.
     *
     * @param int $payment - Payment ID.
     * @param string $type - Type of payment ('order' or 'purchase').
     */
    public function edit($payment, $type): ?View
    {
        return PaymentViewFactory::select($type,$payment);
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
            $builder = PaymentViewFactory::getInstanceModel($payment, $type);

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
        
        // Validate payment method
        if (!$data['payment_method'] || !$this->helper->statusValidation($data['payment_method'], $this->paymentMethods)) {
            throw new \Exception('Invalid payment method.');
        }

        // Validate payment status and method
        if ($data['is_it_delivered']) {
            $expectedDateOfPayment = $builder->expected_date_of_payment;
            $expectedDeliveryDate  =$builder->$relation->expected_delivery_date;

            $date_of_payment = $data['date_of_payment'];
            $delivery_date = $data['delivery_date'];

            // Set payment status based on date of payment and expected date of payment
            $statusDateOfPayment = ($date_of_payment > $expectedDateOfPayment) ? $builder::OVERDUE : $builder::SUCCESSFULLY_PAID_DELIVERED;
            $statusDeliveryDate = ($delivery_date > $expectedDeliveryDate) ? $builder::OVERDUE : $builder::SUCCESSFULLY_PAID_DELIVERED;

            // Update relation attributes
            $builder->$relation->is_it_delivered = $builder->$relation::IS_IT_DELIVERED_TRUE;
            $builder->$relation->delivery_date = now()->parse($delivery_date);

        } else {
            // Update relation attributes for not delivered
            $builder->$relation->is_it_delivered = $builder->$relation::IS_IT_DELIVERED_FALSE;
            $builder->$relation->delivery_date = null;
        }
        $builder->$relation->save();

        // Set common attributes
        $builder->payment_status = $data['is_it_delivered'] ? $statusDateOfPayment : $builder::PENDING;
        $builder->delivery_status = $data['is_it_delivered'] ? $statusDeliveryDate : $builder::PENDING;
        $builder->date_of_payment = $data['is_it_delivered'] ? now()->parse($date_of_payment) : null;

        // Set other attributes
        $builder->payment_reference = $data['payment_reference'];
        $builder->payment_method = $data['payment_method'];

        // Save the main model
        $builder->save();
    
        // Update invoice attributes
        $builder->invoice()->update([
            'price' => $builder->price,
            'quantity' => $builder->quantity
        ]);
    }

    private function validatePaymentMethod(array $data)
    {
        if (empty($data['payment_method']) || !$this->helper->statusValidation($data['payment_method'], $this->paymentMethods)) {
            throw new \Exception('Invalid payment method.');
        }
    }
}
