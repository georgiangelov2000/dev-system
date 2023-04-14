@extends('app')
@section('title', 'Orders')

@section('content')
    <div class="row justify-content-between mb-3">
        <div class="col-12">
            <h3 class="mb-0">Orders</h3>
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
                            <select class="form-control selectAction">
                                <option value="0">Select Option</option>
                                <option value="delete">Delete</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Customers</label>
                            <select class="form-control selectCustomer" data-live-search="true">
                                <option value='9999'>All</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-3">
                        <label for="customRange1">Date of sale</label>
                        <input type="text" class="form-control pull-right" name="datetimes" />
                    </div>
                    <div class="form-group col-3">
                        <label for="">Order status</label>
                        <select name="status" id="" class="form-control selectType">
                            @foreach (config('statuses.order_statuses') as $key => $status)
                                <option value="{{ $key }}">{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <table id="ordersTable" class="table  table-hover table-sm dataTable no-footer">
                    <thead>
                        <tr>
                            <th>
                                <div class="form-check">
                                    <input class="form-check-input selectAll" type="checkbox">
                                    <label class="form-check-label" for="flexCheckDefault"></label>
                                </div>
                            </th>
                            <th>ID</th>
                            <th>Invoice number</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Single price</th>
                            <th>Total Price</th>
                            <th>Discount %</th>
                            <th>Date of sale</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @push('scripts')
        <script type="text/javascript" src="{{ mix('js/orders/orders.js') }}"></script>
        <script type="text/javascript">
            let ORDER_API_ROUTE = "{{ route('api.orders') }}";
            let CUSTOMER_API_ROUTE = "{{route('api.customers')}}";
            let ORDER_UPDATE_STATUS = "{{route('order.status',':id')}}";
            let ORDER_DELETE_ROUTE = "{{route('order.delete',':id')}}";
        </script>
    @endpush
@endsection
