@extends('app')

@section('content')
    <div class="row">
        <div class="card col-12 cardTemplate">
            <div class="card-header">
                <div class="col-12">
                    <h3 class="card-title">Purchases</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <p class="bg-dark p-2 font-weight-bold filters">
                            <i class="fa-solid fa-filter"></i> Filters
                        </p>
                    </div>
                    <div class="col-3 actions d-none">
                        <div class="form-group">
                            <label>Actions</label>
                            <select class="form-control selectAction" title="Choose one of the following...">
                                <option value="0">Select option</option>
                                <option value="delete">Delete</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Suppliers</label>
                            <select name="supplier_id" class="form-control selectSupplier" data-live-search="true">
                            </select>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Categories</label>
                            <select class="form-control selectCategory" data-live-search="true"></select>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Sub categories</label>
                            <select class="form-control selectSubCategory" multiple></select>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Brands</label>
                            <select class="form-control selectBrands" multiple data-live-search="true"></select>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Total price range</label>
                            <select class="form-control selectPrice">
                                <option value="">All</option>
                                <option value="0-1000">0-1000</option>
                                <option value="1000-2000">1000-2000</option>
                                <option value="2000-3000">2000-3000</option>
                                <option value="3000-4000">3000-4000</option>
                                <option value="5000-6000">5000-6000</option>
                                <option value="7000-8000">7000-8000</option>
                                <option value="9000-10000">9000-10000</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Custom total price</label>
                            <input type="text" placeholder="Enter price" class="form-control customPrice">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="stock">Stock</label>
                            <select class="form-control selectStock" name="" id="stock">
                                <option value="">Nothing selected</option>
                                @foreach (config('statuses.stock_statuses') as $key => $status)
                                    <option value="{{ $key }}">{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="" class="form-control selectType" multiple>
                                @foreach (config('statuses.order_statuses') as $key => $status)
                                    @if($key !== 6)
                                        <option value="{{ $key }}">{{ $status }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>     
                </div>  
                <table id="purchasedProducts" class="table table-hover table-sm dataTable no-footer">
                    <thead>
                        <th></th>
                        <th>
                            <div class="form-check">
                                <input class="form-check-input selectAll" type="checkbox">
                                <label class="form-check-label" for="flexCheckDefault"></label>
                            </div>
                        </th>
                        <th>Payment</th>
                        <th>ID</th>
                        <th class='text-center'>Image</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Disc.price</th>
                        <th>Tot.price</th>
                        <th>Orig.price</th>
                        <th>Quantity</th>
                        <th>Init.quantity</th>
                        <th>Discount</th>
                        <th>Stock</th>
                        {{-- <th>Purchases</th> --}}
                        <th>Supplier</th>
                        <th>Category</th>
                        <th>Subcategories</th>
                        <th>Brands</th>
                        <th>Status</th>
                        <th title="Expected date of payment">EDT</th>
                        <th>Paid</th>
                        <th>Actions</th>
                    </thead>
                </table>
            </div>
        </div>
    </div>

      @push('scripts')
        <script type="text/javascript" src="{{ mix('js/purchases/purchases.js') }}"></script>
        <script type='text/javascript'>
            let PRODUCT_API_ROUTE = "{{ route('api.products') }}";
            let CATEGORY_API_ROUTE = "{{ route('api.categories') }}";
            let SUPPLIER_API_ROUTE = "{{ route('api.suppliers') }}";
            let BRAND_API_ROUTE = "{{ route('api.brands') }}";
            let PREVIEW_ROUTE = "{{ route('purchase.preview',':id') }}";
            let REMOVE_PRODUCT_ROUTE = "{{ route('purchase.delete', ':id') }}";
            let EDIT_PRODUCT_ROUTE = "{{ route('purchase.edit', ':id') }}";
            let EDIT_SUPPLIER_ROUTE = "{{route('supplier.edit',':id')}}";
            let CATEGORY_ROUTE = "{{ route('api.subcategories') }}";
            let CONFIG_URL = "{{config('app.url')}}";
            let ORDERS = "{{route('purchase.orders',':id')}}";
            let PAYMENT = "{{route('payment.purchase.edit',':id')}}";
        </script>
    @endpush

@endsection
