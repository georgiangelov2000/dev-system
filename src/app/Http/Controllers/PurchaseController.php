<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\LoadStaticData;
use App\Models\Purchase;
use App\Http\Requests\PurchaseRequest;
use App\Models\SubCategory;
use App\Models\PurchasePayment;
use App\Helpers\FunctionsHelper;
use App\Http\Requests\PurchaseMassEditRequest;
use App\Services\PurchaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PurchaseController extends Controller
{
    const INIT_STATUS = 2;

    private $staticDataHelper;

    private $helper;

    private $dir = 'public/images/products';

    private $statuses;

    private $service;

    public function __construct(
        LoadStaticData $staticDataHelper, 
        FunctionsHelper $helper,
        PurchaseService $service
    )
    {
        $this->staticDataHelper = $staticDataHelper;
        $this->helper = $helper;
        $this->service = $service;
        $this->statuses = config('statuses.payment_statuses');
    }

    public function index()
    {
        return view('purchases.index');
    }

    public function create()
    {
        return view('purchases.create');
    }

    public function store(PurchaseRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            $this->service->purchaseProcessing($data);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('purchase.index')->with('error', $e->getMessage());
        }
        return redirect()->route('purchase.index')->with('success', 'Purchase has been created');
    }
    
    public function edit(Purchase $purchase)
    {
        $orderAmounts = $purchase->orders->sum('sold_quantity');
    
        // Assuming $purchaseId is the ID you want to use in the query
        $paymentRecord = PurchasePayment::where('purchase_id', $purchase->id)->first();
    
        // Check if the payment record exists and is not null
        $isEditable = $paymentRecord && $this->helper->statusValidation($paymentRecord->payment_status, $this->statuses) === Purchase::PENDING;
    
        if (!$isEditable) {
            $purchase->expected_delivery_date = now()->parse($purchase->expected_delivery_date)->format('d F Y');
            $purchase->expected_date_of_payment = now()->parse(optional($purchase->payment)->expected_date_of_payment)->format('d F Y');
            $purchase->delivery_date = now()->parse($purchase->delivery_date)->format('d F Y');
        }
        
        $purchase->status = !$isEditable ? $this->statuses[$paymentRecord->payment_status] : null;
        $purchase->order_amount = $orderAmounts;
        $purchase->is_editable = $isEditable;
    
        return view('purchases.edit', compact('purchase'));
    }

    public function update(Purchase $purchase, PurchaseRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            $this->service->purchaseProcessing($data, $purchase);

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
            $ids = $validated['purchase_ids'];
            
            unset($validated['purchase_ids']);

            // Check if all values in $validated are null
            if (empty(array_filter($validated, function ($value) {
                return $value !== null;
            }))) {
                throw new \Exception('At least one field must be filled.');
            }

            if(count($ids)) {
                $purchases = Purchase::whereIn('id', $ids)->get();
            
                foreach ($ids as $id) {
                    $purchase = $purchases->firstWhere('id', $id);
            
                    if(!$purchase) {
                        throw new \Exception('Purchase with ID ' . $id . ' not found.');
                    }
                    
                    $this->service->purchaseMassEditProcessing($purchase,$validated);
                }
            } else {
                throw new \Exception('No purchase IDs provided for update.');
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Purchases has been updated'], 200);
    }

    public function delete(Purchase $purchase)
    {
        DB::beginTransaction();

        try {

            if ($purchase->is_it_delivered) {
                throw new \Exception("Invalid operation: Purchase with ID {$id} has already been delivered.");
            }            
            
            $imagePath = str_replace('/storage', '', $purchase->image_path);

            // Check if the image path exists and delete it
            if ($purchase->image_path && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            
            $purchase->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
        return response()->json(['message' => 'Purchase has been deleted'], 200);
    }

    public function orders(Purchase $purchase)
    {
        return view('purchases.orders', compact('purchase'));
    }
}
