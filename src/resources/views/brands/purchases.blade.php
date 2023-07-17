@extends('app')

@section('content')

    <div class="row">
        <div class="card col-12 cardTemplate">

            <div class="card-header d-flex align-items-center p-2">
                <div class="col-10">
                    <h3 class="card-title">Purchases for {{$brand->name}}</h3>
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
                                <option value="delete">Detach</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-3">
                        <label for="customRange1">Publishing</label>
                        <input type="text" class="form-control pull-right" name="datetimes" />
                    </div>
                    <div class="form-group col-3">
                        <div class="form-group">
                            <label for="stock">Stock</label>
                            <select class="form-control selectStock" name="" id="stock">
                                <option selected value="1">In stock</option>
                                <option value="0">Out of stock</option>
                            </select>
                        </div>
                    </div>                    
                </div>
                <table id="purchasesTable" class="table  table-hover table-sm dataTable no-footer">
                    <thead>
                        <tr>
                            <th>
                                Status
                            </th>
                            <th>
                                <div class="form-check">
                                    <input class="form-check-input selectAll" type="checkbox">
                                    <label class="form-check-label" for="flexCheckDefault"></label>
                                </div>
                            </th>
                            <th>Image</th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Tot.price</th>
                            <th>Quantity</th>
                            <th>Init.quantity</th>
                            <th>Stock</th>
                            <th>Balance</th>
                            <th>Supplier</th>
                            <th>Categories</th>
                            <th>Subcategories</th>
                            <th>Code</th>
                            <th>Created</th>
                            <th>Paid</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script type="text/javascript" src="{{ mix('js/brands/purchases.js') }}"></script>

        <script type="text/javascript">
            const BRAND = "{{$brand->id}}";
            const PURCHASE_API_ROUTE = "{{route('api.products')}}";
            const CONFIG_URL = "{{config('app.url')}}";
            const EDIT_SUPPLIER_ROUTE = "{{ route('supplier.edit', ':id') }}";
            const EDIT_PRODUCT_ROUTE = "{{ route('purchase.edit', ':id') }}";
            const DETACH_PRODUCT = "{{route('brand.detach.purchase',':id')}}";
        </script>
    @endpush

@endsection
