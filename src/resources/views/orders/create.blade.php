@extends('app')

@section('content')
    <div class="card card-default cardTemplate">
        <div class="card-header bg-primary">
            <div class="col-12">
                <h3 class="card-title">Create order</h3>
            </div>
        </div>
        <div class="card-body">

            <div class="col-12">

                <form id="orderForm" action="{{ route('order.store') }}" method="POST">
                    @csrf

                    <div class="row flex-wrap">

                        <div class="form-group col-xl-2 col-lg-4 col-md-4 col-sm-4 mb-0">
                            <label for="customer_id">Customer</label>
                            <select id="customer_id" name="customer_id" class="form-control selectCustomer"
                                data-live-search="true">
                            </select>
                            <span name="customer_id" class="text-danger"></span>
                        </div>

                        <div class="form-group col-xl-2 col-lg-4 col-md-4 col-sm-4 mb-0">
                            <label for="user_id">Assign to driver</label>
                            <select id="user_id" name="user_id" class="form-control selectUser"
                                data-live-search="true">
                            </select>
                            <span name="user_id" class="text-danger"></span>
                        </div>

                        <div class="form-group col-xl-2 col-lg-4 col-md-4 col-sm-4 mb-0">
                            <label for="package_id">Assign to Package</label>
                            <select id="package_id" name="package_id" class="form-control selectPackage"
                                data-live-search="true">
                            </select>
                            <span name="package_id" class="text-danger"></span>
                        </div>

                        <div class="form-group col-xl-3 col-lg-4 col-md-4 col-sm-4 mb-0">
                            <label>Date of sale:</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="far fa-calendar-alt"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control datepicker" name="date_of_sale">
                            </div>
                            <span name="date_of_sale" class="text-danger"></span>
                        </div>

                        <div class="form-group col-xl-3 col-lg-4 col-md-4 col-sm-4 ">
                            <label for="">Search purchase</label>
                            <select name="" id="" class="productFilter form-control" data-live-search="true">
                            </select>
                        </div>

                    </div>

                    <div class="row table-responsive">
                        <table class="table table-hover table-sm productOrderTable ">
                            <thead>
                                <th>Actions</th>
                                <th>Image</th>
                                <th>Purchase</th>
                                <th>Unit price</th>
                                <th>Amount</th>
                                <th>Order amount</th>
                                <th>Order unit price</th>
                                <th>Order discount %</th>
                                <th>Tracking number</th>
                                <th>Order final price</th>
                                <th>Order regular price</th>
                            </thead>
                        </table>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>

        </div>
    </div>

    @push('scripts')
        <script type="text/javascript" src="{{ mix('js/helpers/render_helpers.js') }}"></script>
        <script type="text/javascript" src="{{ mix('js/orders/form.js') }}"></script>
        <script type="text/javascript">
            const ORDER_INDEX_ROUTE = "{{route('order.index')}}";
            const PURCHASE_ROUTE = "{{ route('purchase.edit',':id') }}"
            const CUSTOMER_API_ROUTE = "{{ route('api.customers') }}";
            const PRODUCT_API_ROUTE = "{{ route('api.products') }}";
            const USER_API_ROUTE = "{{route('api.users')}}";
            const PACKAGE_API_ROUTE  = "{{ route('api.packages') }}";
        </script>
    @endpush

@endsection
