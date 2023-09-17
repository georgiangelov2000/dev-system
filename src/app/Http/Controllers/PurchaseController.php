<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\LoadStaticData;
use App\Models\Purchase;
use App\Http\Requests\PurchaseRequest;
use App\Models\SubCategory;
use App\Helpers\FunctionsHelper;
use App\Http\Requests\PurchaseMassEditRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PurchaseController extends Controller
{

    private $staticDataHelper;

    private $helper;

    private $dir = 'public/images/products';

    private $statuses;

    public function __construct(LoadStaticData $staticDataHelper, FunctionsHelper $helper)
    {
        $this->staticDataHelper = $staticDataHelper;
        $this->helper = $helper;
        $this->statuses = config('statuses.purchase_statuses');
    }

    public function index()
    {
        return view('purchases.index');
    }

    public function create()
    {
        $data = $this->loadStaticData();
        return view('purchases.create', $data);
    }

    public function store(PurchaseRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            $this->purchaseProcessing($data);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('purchase.index')->with('error', 'Purchase has not been created');
        }
        return redirect()->route('purchase.index')->with('success', 'Purchase has been created');
    }
    public function edit(Purchase $purchase)
    {
        $relatedProductData = $this->fetchRelatedProductData($purchase);
        $data = $this->loadStaticData();
        $orderAmount = $purchase->orders->sum('sold_quantity');

        $isEditable = array_key_exists($purchase->status, $this->statuses) && $purchase->status === 6 ? true : false;
        $status = array_key_exists($purchase->status, $this->statuses) ? $this->statuses[$purchase->status] : '';


        return view(
            'purchases.edit',
            compact(
                'purchase',
                'orderAmount',
                'relatedProductData',
                'isEditable',
                'status'
            ),
            $data
        );
    }

    public function update(Purchase $purchase, PurchaseRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            $this->purchaseProcessing($data, $purchase);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('purchase.create')->with('error', 'Product has not updated');
        }
        return redirect()->route('purchase.index')->with('success', 'Product has been updated');
    }

    public function preview(Purchase $purchase)
    {
        $purchase->load('brands', 'categories', 'supplier:id,name', 'subcategories', 'images');
        return view('purchases.preview', compact('purchase'));
    }

    public function massEditUpdate(PurchaseMassEditRequest $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validated();

            foreach ($validated['purchases'] as $purchaseId) {
                $this->purchaseMassEditProcessing($validated, $purchaseId);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Purchases has not been updated'], 500);
        }

        return response()->json(['message' => 'Purchases has been updated'], 200);
    }
    public function fetchRelatedProductData($purchaseModel)
    {
        $purchaseModel->load('categories:id', 'subcategories:id', 'brands:id');

        $categorySubCategories = SubCategory::select('id', 'name')
            ->whereIn('category_id', $purchaseModel->categories->pluck('id'))
            ->get()
            ->toArray();

        return [
            'categorySubCategories' => $categorySubCategories,
            'purchaseCategory' => $purchaseModel->categories->pluck('id')->first(),
            'purchaseSubCategories' => $purchaseModel->subcategories->pluck('id')->toArray(),
            'purchaseBrands' => $purchaseModel->brands->pluck('id')->toArray(),
        ];
    }

    public function delete(Purchase $purchase)
    {
        DB::beginTransaction();

        try {
            if ($purchase->payment()->exists()) {
                return response()->json(['message' => 'Payment already exists for this purchase'], 500);
            }

            if (!empty($purchase->images)) {
                $image_names = $purchase->images()->pluck('name');

                foreach ($image_names as $key => $images) {
                    Storage::delete($this->dir . DIRECTORY_SEPARATOR . $images);
                }
            }

            $purchase->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Purchase has not been deleted'], 500);
        }
        return response()->json(['message' => 'Purchase has been deleted'], 200);
    }

    public function orders(Purchase $purchase)
    {
        return view('purchases.orders', compact('purchase'));
    }

    // Private methods

    private function loadStaticData()
    {
        $suppliers = $this->staticDataHelper->callSupliers();
        $brands = $this->staticDataHelper->callBrands();
        $categories = $this->staticDataHelper->loadCallCategories();

        return [
            'suppliers' => $suppliers,
            'brands' => $brands,
            'categories' => $categories
        ];
    }

    private function purchaseProcessing(array $data, $purchase = null)
    {
        $prices = null;

        // Check if $data['image'] exists and set it to $file
        $file = $data['image'] ?? null;

        $purchase = $purchase ? $purchase : new Purchase;
        $isNewPurchase = !$purchase->exists; // Check if it's a new purchase
        $status = $isNewPurchase ? $this->checkForValidStatus(6) : $this->checkForValidStatus($purchase->status);

        // Update purchase or create Purchase;
        $purchase->name = $data['name'];
        $purchase->supplier_id = $data['supplier_id'];
        $purchase->notes = $data['notes'] ?? '';

        if (($purchase && $status === 6) || $isNewPurchase) {

            // Update amount
            if ($data['quantity'] > 0) {
                $purchase->quantity = $data['quantity'];
                $purchase->initial_quantity = $data['quantity'];
            }

            // Check order amount
            $ordersQuantity = $purchase->orders->sum('sold_quantity');
            if ($purchase->initial_quantity < $ordersQuantity) {
                throw new \Exception("Insufficient purchase quantity. The total order quantity exceeds the available purchase quantity.");
            };
            $finalQuantity = ($purchase->initial_quantity - $ordersQuantity);
            $purchase->quantity = $finalQuantity;

            // Calculate prices
            $prices = $this->calculatePrices($data['price'], $data['discount_percent'], $purchase->initial_quantity);

            // Update prices
            $purchase->price = $data['price'];
            $purchase->total_price = $prices['total_price'];
            $purchase->original_price = $prices['original_price'];
            $purchase->discount_price = $prices['discount_price'];
            $purchase->discount_percent = $data['discount_percent'];

            // Update code
            $purchase->code = $data['code'];

            // Update dates
            $purchase->expected_date_of_payment = now()->parse($data['expected_date_of_payment']);
            $purchase->delivery_date = now()->parse($data['delivery_date']);
            $purchase->status = $status;
        }

        $purchase->save();

        $alias = now()->parse($data['delivery_date'])->format('F j, Y');
        $alias = str_replace([' ', ','], ['_', ''], $alias);
        $alias = strtolower($alias);

        $paymentData = [
            'alias' => $alias,
            'quantity' => $purchase->initial_quantity,
            'price' => $purchase->total_price,
            'date_of_payment' => $purchase->expected_date_of_payment
        ];

        $payment = $purchase->payment()->updateOrCreate([], $paymentData);

        $payment->invoice()->updateOrCreate([], [
            'price' => $payment->price,
            'quantity' => $payment->quantity
        ]);

        if (array_key_exists('category_id', $data) && !empty($data['category_id'])) {
            $purchase->categories()->sync($data['category_id']);
        }

        if (array_key_exists('subcategories', $data) && !empty($data['subcategories'])) {
            $purchase->subcategories()->sync($data['subcategories']);
        }

        if (array_key_exists('brands', $data) && !empty($data['brands'])) {
            $purchase->brands()->sync($data['brands']);
        }

        if ($file) {
            $hashed_image = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
            Storage::putFileAs($this->dir, $file, $hashed_image);
            $purchase->image_path = $this->getImagePath() . '/' . $hashed_image;
        }
    }

    private function purchaseMassEditProcessing(array $data, $id)
    {
        $purchase = Purchase::find($id);

        if ($data['quantity'] && is_int(intval($data['quantity']))) {
            $purchase->quantity = $data['quantity'];
            $purchase->initial_quantity = $data['quantity'];
        }
        if (is_numeric($data['price'])) {
            $purchase->price = $data['price'];
        }

        if ($data['discount_percent'] && is_int(intval($data['discount_percent']))) {
            $purchase->discount_percent = $data['discount_percent'];
        }

        $ordersQuantity = $purchase->orders->sum('sold_quantity');
        if ($purchase->initial_quantity < $ordersQuantity) {
            throw new \Exception("Insufficient purchase quantity. The total order quantity exceeds the available purchase quantity.");
        };
        $finalQuantity = ($purchase->initial_quantity - $ordersQuantity);
        $purchase->quantity = $finalQuantity;

        $prices = $this->calculatePrices(
            $purchase->price,
            $purchase->discount_percent,
            $purchase->quantity
        );

        $purchase->total_price = $prices['total_price'];
        $purchase->original_price = $prices['original_price'];
        $purchase->discount_price = $prices['discount_price'];

        $purchase->save();

        $paymentData = [
            'alias' => $purchase->delivery_date->format('F j, Y'),
            'quantity' => $purchase->quantity->format('F j, Y'),
            'price' => $purchase->price->format('F j, Y'),
            'date_of_payment' => $purchase->expected_date_of_payment
        ];

        $payment = $purchase->payment()->updateOrCreate([], $paymentData);

        $payment->invoice()->updateOrCreate([], [
            'price' => $payment->price,
            'quantity' => $payment->quantity
        ]);

        if (array_key_exists('category_id', $data) && !empty($data['category_id'])) {
            $purchase->categories()->sync($data['category_id']);
        }

        if (array_key_exists('sub_category_ids', $data) && !empty($data['sub_category_ids'])) {
            $purchase->subcategories()->sync($data['sub_category_ids']);
        }

        if (array_key_exists('brands', $data) && !empty($data['brands'])) {
            $purchase->brands()->sync($data['brands']);
        }
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

    private function getImagePath(): string
    {
        return Storage::url($this->dir);
    }

    private function checkForValidStatus(int $status): int
    {
        $statuses = config('statuses.purchase_statuses');
        return array_key_exists($status, $statuses) ? $status : null;
    }
}
