@extends('app')
@section('title', 'Purchases')

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
                            <select class="form-control selectSupplier">
                                <option value=''>All</option>
                                @foreach ($suppliers as $supplier )
                                    <option value='{{$supplier->id}}'>{{$supplier->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Categories</label>
                            <select class="form-control selectCategory">
                                <option value=''>All</option>
                                @foreach ($categories as $category )
                                    <option value='{{$category->id}}'>{{$category->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Sub categories</label>
                            <select class="form-control selectSubCategory" multiple>
                            </select>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Brands</label>
                            <select class="form-control selectBrands" multiple>
                                <option value=''>All</option>
                                @foreach ($brands as $brand )
                                    <option value='{{$brand->id}}'>{{$brand->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Select total price range</label>
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
                        <label for="customRange1">Publishing</label>
                        <input type="text" class="form-control pull-right" name="datetimes" />
                    </div>
                </div>  
                <table id="purchasedProducts" class="table table-hover table-sm dataTable no-footer">
                    <thead>
                        <th>
                            <div class="form-check">
                                <input class="form-check-input selectAll" type="checkbox">
                                <label class="form-check-label" for="flexCheckDefault"></label>
                            </div>
                        </th>
                        <th>Status</th>
                        <th>ID</th>
                        <th class='text-center'>Image</th>
                        <th>Name</th>
                        <th>Single price</th>
                        <th>Total price</th>
                        <th>Quantity</th>
                        <th>Notes</th>
                        <th>Supplier</th>
                        <th>Category</th>
                        <th>Subcategories</th>
                        <th>Brands</th>
                        <th>Code</th>
                        <th>Created at</th>
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
            let PREVIEW_ROUTE = "{{ route('purchase.preview',':id') }}";
            let REMOVE_PRODUCT_ROUTE = "{{ route('purchase.delete', ':id') }}";
            let EDIT_PRODUCT_ROUTE = "{{ route('purchase.edit', ':id') }}";
            let EDIT_SUPPLIER_ROUTE = "{{route('supplier.edit',':id')}}";
            let CATEGORY_ROUTE = "{{ route('api.categories') }}";
        </script>
    @endpush

@endsection
