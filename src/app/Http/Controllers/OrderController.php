<?php

namespace App\Http\Controllers;

use App\Helpers\FunctionsHelper;
use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\OrderMassEditRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Package;
use App\Models\Purchase;
use App\Helpers\LoadStaticData;

class OrderController extends Controller
{
    private $staticDataHelper;
    private $helper;

    const DELIVERED_STATUS = 6;

    public function __construct(LoadStaticData $staticDataHelper, FunctionsHelper $helper)
    {
        $this->staticDataHelper = $staticDataHelper;
        $this->helper = $helper;
    }

    public function index()
    {
        return view('orders.index');
    }
    public function create()
    {
        return view('orders.create');
    }

    public function store(OrderRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {

            if (count($data['purchase_id'])) {
                foreach ($data['purchase_id'] as $key => $id) {
                    $purchase = Purchase::find($id);

                    $orderQ = $data['sold_quantity'][$key];
                    $orderSinglePrice = $data['single_sold_price'][$key];
                    $orderDiscount = $data['discount_percent'][$key];

                    if ($purchase->initial_quantity < $orderQ) {
                        return back()->with('error', 'Purchase quantity is not enough' . $purchase->name);
                    }

                    $purchase->quantity -= $orderQ;

                    $purchase->save();

                    $prices = $this->calculatePrices(
                        $orderSinglePrice,
                        $orderDiscount,
                        $orderQ
                    );

                    $ext_date = null;
                    $package_id = null;
                    if (array_key_exists('package_id', $data) && $data['package_id']) {
                        $ext_date = Package::find($data['package_id'])->expected_delivery_date;
                        $package_id = $data['package_id'];
                    }

                    // Create an Order object
                    $order = Order::create([
                        'customer_id' => $data['customer_id'][$key],
                        'user_id' => $data['user_id'][$key],
                        'purchase_id' => $id,
                        'sold_quantity' => $orderQ,
                        'single_sold_price' => $orderSinglePrice,
                        'discount_single_sold_price' => $prices['discount_price'],
                        'total_sold_price' => $prices['total_price'],
                        'original_sold_price' =>  $prices['original_price'],
                        'discount_percent' => $data['discount_percent'][$key],
                        'date_of_sale' => now()->parse($data['date_of_sale']),
                        'tracking_number' => $data['tracking_number'],
                        'package_id' => $package_id,
                        'package_extension_date' => $ext_date,
                        'status' => self::DELIVERED_STATUS,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    // Call the createOrUpdatePayment method with the Order object
                    $this->createOrUpdatePayment($order);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Order has not been created'], 500);
        }
        return response()->json(['message' => 'Order has been created'], 200);
    }

    public function edit(Order $order)
    {
        $order->load(
            'customer:id,name',
            'user:id,username',
            'purchase.categories',
            'purchase.brands',
            'purchase'
        );
        return view('orders.edit', compact('order'));
    }

    public function update(Order $order, OrderRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $this->orderUpdateProcessing($data, $order);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $e->getMessage();
            return back()->withInput()->with('error', 'Order has not been updated');
        }
        return redirect()->route('order.index')->with('success', 'Order has been updated');
    }

    public function massUpdate(OrderMassEditRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            if (count($data['order_ids'])) {
                foreach ($data['order_ids'] as $key => $value) {
                    $order = Order::find($value);
                    $this->orderUpdateProcessing($data, $order);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Orders has not been updated', 500]);
        }
        return response()->json(['message' => 'Orders has been updated'], 200);
    }

    public function updateStatus(Order $order, Request $request)
    {
        try {

            $specificColumns = $request->only(['status', 'detach_package']);

            $detachPackage = isset($specificColumns['detach_package'])
                && $specificColumns['detach_package'] == true ? true : false;

            if ($detachPackage) {
                $package = $order->packages()->first();
                if ($package) {
                    $order->packages()->detach($package->id);
                    $order->package_extension_date = null;
                }
            }

            $order->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Order has not been updated'], 500);
        }
        return response()->json(['message' => 'Order has been updated'], 200);
    }

    public function delete(Order $order)
    {
        DB::beginTransaction();

        try {
            $product = $order->product;
            $orderQuantity = $order->sold_quantity;

            if (!$product) {
                throw new \Exception("Purchase not found");
            }

            $product->quantity += $orderQuantity;
            $product->save();

            $order->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Order has not been deleted');
        }
        return response()->json(['message' => 'Order has been deleted'], 200);
    }

    // Private methods
    private function orderUpdateProcessing(array $data, $order)
    {
        if (array_key_exists('customer_id', $data) && $data['customer_id']) {
            $order->customer_id = $data['customer_id'];
        }
        if (array_key_exists('user_id', $data) && $data['user_id']) {
            $order->user_id = $data['user_id'];
        }
        if (array_key_exists('package_id', $data) && $data['package_id']) {
            $order->package_id = $data['package_id'];
            $order->package_extension_date = Package::find($data['package_id'])->expected_delivery_date;
        }
        if (array_key_exists('purchase_id', $data) && $data['purchase_id']) {
            $order->purchase_id = $data['purchase_id'];
        }

        if (($order && $order->status === 6)) {
            if ($data['date_of_sale']) {
                $order->date_of_sale = now()->parse($data['date_of_sale']);
            }
            if ($data['sold_quantity'] && is_int(intval($data['sold_quantity']))) {
                $order->sold_quantity = $data['sold_quantity'];
            }
            if ($data['single_sold_price'] && is_numeric($data['single_sold_price'])) {
                $order->single_sold_price = $data['single_sold_price'];
            }
            if ($data['discount_percent'] && is_int(intval($data['discount_percent']))) {
                $order->discount_percent = $data['discount_percent'];
            }
            if (array_key_exists('tracking_number', $data) && $data['tracking_number']) {
                $order->tracking_number = $data['tracking_number'];
            }

            $newSingleSoldPrice = $order->single_sold_price;
            $newDiscountPercentage = $order->discount_percent;
            $newSoldQua = $order->sold_quantity;

            // Find related purchase
            $purchase = Purchase::findOrFail($order->purchase_id);

            //Calculate quantity of the purchase
            $totalSoldQuantity = $purchase->orders->sum('sold_quantity');
            $remainingQuantity = ($totalSoldQuantity - $order->getOriginal('sold_quantity'));
            $updatedQuantity = ($remainingQuantity + $newSoldQua);

            if ($updatedQuantity > $purchase->initial_quantity) {
                return response()->json(['message', 'Purchase quantity is not enough']);
            }

            $finalQuantity = ($purchase->initial_quantity - $updatedQuantity);
            $purchase->quantity = $finalQuantity;
            $purchase->save();

            $prices = $this->calculatePrices(
                $newSingleSoldPrice,
                $newDiscountPercentage,
                $newSoldQua
            );

            $order->sold_quantity = $newSoldQua;
            $order->single_sold_price = $newSingleSoldPrice;
            $order->discount_single_sold_price = $prices['discount_price'];
            $order->total_sold_price = $prices['total_price'];
            $order->original_sold_price = $prices['original_price'];
        }

        $order->save();

        $this->createOrUpdatePayment($order);
    }

    private function calculatePrices($price, $discount, $quantity): array
    {

        $discountPrice = $this->helper->calculatedDiscountPrice($price, $discount);
        $totalPrice = $this->helper->calculatedFinalPrice($discountPrice, $quantity);
        $originalPrice = $this->helper->calculatedFinalPrice($price, $quantity);

        return [
            'discount_price' => $discountPrice,
            'total_price' => $totalPrice,
            'original_price' => $originalPrice
        ];
    }

    private function createOrUpdatePayment($order)
    {
        $alias = $this->getAlias($order);

        $paymentData = [
            'alias' => $alias,
            'quantity' => $order->sold_quantity,
            'price' => $order->total_sold_price,
            'date_of_payment' => $this->getDateOfPayment($order)
        ];

        $payment = $order->payment()->updateOrCreate([], $paymentData);

        $payment->invoice()->updateOrCreate([], [
            'price' => $payment->price,
            'quantity' => $payment->quantity
        ]);
    }

    private function getAlias($order)
    {
        $aliasDate = $order->package_extension_date
            ? now()->parse($order->package_extension_date)->format('F j, Y')
            : now()->parse($order->date_of_sale)->format('F j, Y');

        return strtolower(str_replace([' ', ','], ['_', ''], $aliasDate));
    }

    private function getDateOfPayment($order)
    {
        return $order->package_extension_date
            ? $order->package_extension_date
            : $order->date_of_sale;
    }
}
