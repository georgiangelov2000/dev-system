<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Http\Requests\PaymentRequest;
use App\Models\Purchase;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\PurchasePayment;
use App\Models\Settings;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaymentController extends Controller
{
    private $types = ['order', 'purchase'];

    private $indexMapping = [];

    private $editMapping = [];

    private $paymentStatuses;

    private $paymentMethods;

    const DEFAULT_STATUS = 6;

    public function __construct()
    {
        $this->indexMapping = [
            'order' => [
                'customers' => Customer::select('id', 'name')->get()
            ],
            'purchase' => [
                'suppliers' => Supplier::select('id', 'name')->get()
            ]
        ];

        $this->editMapping = [
            'order' => OrderPayment::query(),
            'purchase' => PurchasePayment::query(),
        ];

        $this->paymentStatuses = config('statuses.payment_statuses');
        $this->paymentMethods  = config('statuses.payment_methods_statuses');
    }

    public function index($type)
    {
        if (in_array($type, $this->types)) {
            return $this->getView($type);
        } else {
            throw new NotFoundHttpException;
        }
    }

    public function edit($payment, $type)
    {
        if (in_array($type, $this->types)) {
            return $this->getView($type, $payment);
        } else {
            throw new NotFoundHttpException;
        }
    }

    public function update(PaymentRequest $request, $payment, $type)
    {
        DB::beginTransaction();

        try {
            $builder = $this->editMapping[$type]->findOrFail($payment);

            if ($builder instanceof OrderPayment) {
                $relation = 'order';
            } elseif ($builder instanceof PurchasePayment) {
                $relation = 'purchase';
            }

            $data = $request->validated();

            $this->paymentProcessing($data, $builder, $relation);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Payment has not been updated');
        }

        return redirect()->back()->with('success', 'Payment has been updated');
    }

    public function delete($payment, $type)
    {
        DB::beginTransaction();
    
        try {
            $builder = $this->editMapping[$type]->findOrFail($payment);
    
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
        

    // Private methods
    private function getView(string $type, ?string $payment = null)
    {
        if (array_key_exists($type, $this->indexMapping) && !$payment) {
            $data = $this->indexMapping[$type];
            $view = view('payments.' . $type . '_payments', $data);
        } elseif (array_key_exists($type, $this->editMapping) && $payment) {
            $relations = [];
            $builder = $this->editMapping[$type]->where('id', $payment)->first();

            if ($builder instanceof OrderPayment) {
                array_push($relations, 'order.customer', 'invoice');
            } elseif ($builder instanceof PurchasePayment) {
                array_push($relations, 'purchase.supplier', 'invoice');
            }
            $builder->load($relations);

            $data['payment'] = $builder;
            $data['settings'] = $this->settings();

            $view = view($type . 's.' . 'edit' . '_payment', $data);
        }

        return $view ?? throw new NotFoundHttpException;;
    }

    private function settings()
    {
        $jsonEncoded = Settings::where('type', 1)->first();
        $jsonDecoded = json_decode($jsonEncoded->settings, true);

        return $jsonDecoded;
    }

    private function paymentProcessing(
        array $data,
        $builder,
        $relation
    ) {
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
