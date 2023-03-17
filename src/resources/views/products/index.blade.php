@extends('app')
@section('title', 'Purchases')

@section('content')
<div class="row justify-content-between mb-3">
    <div class="col-12 d-flex justify-content-between">
        <h3 class="mb-0">Purchases</h3>
    </div>
</div>
<div class="row">
    <div class="card col-12 cardTemplate">
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
            </div>
            <table id="purchasedProducts" class="table  table-hover table-sm dataTable no-footer">
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
                <th>Price (Single price)</th>
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
    <script type="text/javascript" src="{{mix('js/products/products.js')}}"></script>
    <script type='text/javascript'>
        let PRODUCT_API_ROUTE = "{{route('api.products')}}";
        let REMOVE_PRODUCT_ROUTE = "{{route('product.delete',':id')}}";
        let EDIT_PRODUCT_ROUTE = "{{route('product.edit',':id')}}";
    </script>
@endpush()

@endsection