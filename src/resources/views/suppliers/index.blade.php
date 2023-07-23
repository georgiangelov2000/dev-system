@extends('app')

@section('content')
<div class="row">
    <div class="card col-12 cardTemplate">
        <div class="card-header">
            <div class="col-12">
                <h3 class="card-title">Suppliers</h3>
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
                        <select class="form-control selectAction">
                            <option value="0">Select Option</option>
                            <option value="delete">Delete</option>
                        </select>
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Country</label>
                        <select class="form-control selectCountry" >
                            <option value='0' >All</option>
                            @foreach($countries as $country)
                                <option value="{{$country->id}}">{{$country->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>State</label>
                        <select class="form-control selectState">
                            <option value="" disabled>Nothing selected</option>
                        </select>
                    </div>
                </div>
                <div class="col-3">
                    <label>Categories</label>
                    <select class="form-control selectCategory" multiple multiple data-selected-text-format="count > 2">
                        @foreach($categories as $category)
                            <option value="{{$category->id}}" >{{$category->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <table id="suppliersTable" class="table  table-hover table-sm dataTable no-footer">
                <thead>
                    <tr>
                        <th>
                            <div class="form-check">
                                <input class="form-check-input selectAll" type="checkbox">
                                <label class="form-check-label" for="flexCheckDefault"></label>
                            </div>
                        </th>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Website</th>
                        <th>Zip code</th>
                        <th>Country</th>
                        <th>State</th>
                        <th>Categories</th>
                        <th>Notes</th>
                        <th>Purchases</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@push('scripts')
    <script type="text/javascript" src="{{mix('js/suppliers/suppliers.js')}}"></script>
    <script type="text/javascript">
        let SUPPLIER_ROUTE_API_ROUTE = "{{route('api.suppliers')}}";
        let REMOVE_SUPPLIER_ROUTE = "{{route('supplier.delete',':id')}}";
        let EDIT_SUPPLIER_ROUTE = "{{route('supplier.edit',':id')}}";
        let UPDATE_SUPPLIER_ROUTE = "{{route('supplier.update',':id')}}";
        let STORE_SUPPLIER_ROUTE = "{{route('supplier.store')}}";
        let STATE_ROUTE = "{{route('state',':id')}}"; 
        let DETACH_CATEGORY =  "{{route('supplier.detach.category',':id')}}"
        let CONFIG_URL = "{{config('app.url')}}"
        let MASS_EDIT_PURCHASES = "{{route('supplier.mass.edit.purchases',':id')}}"
    </script>
@endpush

@endsection