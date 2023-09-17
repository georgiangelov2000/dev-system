@extends('app')

@section('content')
    <div class="row">
        <div class="card col-12 cardTemplate">
            <div class="card-header">
                <div class="col-12">
                    <h3 class="card-title">Customers</h3>
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
                            <select class="form-control selectCountry">
                                <option value='0'>All</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
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
                </div>               
                <table id="customersTable" class="table table-hover table-sm dataTable no-footer">
                    <thead>
                        <tr>
                            <th>
                                <div class="form-check">
                                    <input class="form-check-input selectAll" type="checkbox">
                                    <label class="form-check-label" for="flexCheckDefault"></label>
                                </div>
                            </th>
                            <th>
                                ID
                            </th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Website</th>
                            <th>Zip code</th>
                            <th>Country</th>
                            <th>State</th>
                            <th>Orders</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@push('scripts')
<script type="text/javascript" src="{{mix('js/customers/customers.js')}}"></script>
    <script type="text/javascript">
        let CUSTOMER_API_ROUTE = "{{route('api.customers')}}";
        let CUSTOMER_DELETE_ROUTE = "{{route('customer.delete',':id')}}";
        let CUSTOMER_EDIT_ROUTE = "{{route('customer.edit',':id')}}";
        let STATE_ROUTE = "{{ route('state', ':id') }}";
        let CUSTOMER_ORDERS_ROUTE = "{{ route('customer.mass.edit.orders', ':id') }}";
    </script>
@endpush
@endsection
