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
use Carbon\Carbon;

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
            return redirect()->route('purchases.index')->with('error', $e->getMessage());
        }
        return redirect()->route('purchases.index')->with('success', 'Purchase has been created');
    }
    
    public function edit(Purchase $purchase)
    {
        $purchase->order_amount = $purchase->orders->sum('sold_quantity');
        $payment = $purchase->payment;
        $purchase->is_editable = $payment->payment_status === Purchase::PENDING ? true : false;
        $purchase->invoice = $purchase->payment->invoice;

        $purchase->category = $purchase->categories()->first();
        $purchase->sub_categories = $purchase->subcategories()->get();
        $purchase->brands = $purchase->brands()->get();
        $purchase->supplier = $purchase->supplier;
        
        if(!$purchase->is_editable) {
            $purchase->expected_delivery_date = now()->parse($purchase->expected_delivery_date)->format('d F Y');
            $purchase->expected_date_of_payment = now()->parse($payment->expected_date_of_payment)->format('d F Y');
            $purchase->delivery_date = now()->parse($purchase->delivery_date)->format('d F Y');
            $purchase->invoice->invoice_date = now()->parse($purchase->invoice->invoice_date)->format('d F Y');
        }

        $purchase->status = $this->statuses[$payment->payment_status]; 
        
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
            return redirect()->route('purchases.create')->with('error', 'Product has not updated');
        }
        return redirect()->route('purchases.index')->with('success', 'Product has been updated');
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

    public function destroy(Purchase $purchase)
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

    public function show (Purchase $purchase) {
        $purchase->load('payment','payment.invoice','supplier','categories','brands');

        $payment = $purchase->payment;
        
        $payment_methods = config('statuses.payment_methods_statuses');

        if($purchase->delivery_date) {
            $purchase->delivery_date = Carbon::parse($purchase->delivery_date)->format('F j, Y');
        }

        if($payment) {
            $payment->payment_status = $this->statuses[$purchase->payment->payment_status] ?? '';
            $payment->payment_method = $payment_methods[$purchase->payment->payment_method] ?? '';
            $payment->expected_date_of_payment = Carbon::parse($payment->expected_date_of_payment)->format('F j, Y');

            if($payment->date_of_payment) {
                $payment->date_of_payment = Carbon::parse($payment->date_of_payment)->format('F j, Y');
            }
        }
        
        $company = $companyInformation = FunctionsHelper::settings();
        return view('purchases.show',compact('purchase','company'));
    }

    public function updateSpecificColumns(Purchase $purchase ,Request $request){
        DB::beginTransaction();

        try {
            $specificColumns = $request->only(['status']);
            $status = intval($specificColumns['status']);

            $payment = $purchase->payment; 
            $paymentStatus = $payment->payment_status;

            if(in_array($paymentStatus,[PurchasePayment::SUCCESSFULLY_PAID_DELIVERED,$paymentStatus === PurchasePayment::OVERDUE])) {
                throw new \Exception("Invalid operation: Purchase with id ${$purchase->id} paid or overdue statuses cannot be refunded");
            }
            
            $payment->payment_status = PurchasePayment::REFUNDED;
            $payment->delivery_status = PurchasePayment::SUCCESSFULLY_PAID_DELIVERED;
            $payment->save();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
        return response()->json(['message' => 'Purchase has been refunded!'], 200);
    }

    public function orders(Purchase $purchase)
    {
        return view('purchases.orders', compact('purchase'));
    }
}
