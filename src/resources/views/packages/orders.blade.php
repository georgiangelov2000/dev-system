@extends('app')

@section('content')
    <div class="card card-default cardTemplate">
        <div class="card-header">
            <div class="col-12">
                <h3 class="card-title">Orders for {{ $package->package_name }}</h3>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <p class="bg-dark p-2 font-weight-bold filters">
                        <i class="fa-solid fa-filter"></i> Filters
                    </p>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Customers</label>
                        <select class="form-control selectCustomer" data-live-search="true">
                            <option value=''>All</option>
                        </select>
                    </div>
                </div>
                <div class="col-3">
                    <label for="customRange1">Date of sale</label>
                    <input type="text" class="form-control pull-right" name="datetimes" />
                </div>
                <div class="form-group col-3">
                    <label for="">Status</label>
                    <select name="status" id="" class="form-control selectType" multiple>
                        @foreach (config('statuses.payment_statuses') as $key => $status)
                            <option value="{{ $key }}">{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <h6 class="font-weight-bold">Legend:</h6>
                </div>
                <div class="col-12">
                    <i title="Reveived" class="fa-solid fa-check"></i>
                    <span>-</span>
                    <span>The order has been delivered to the customer</span>
                </div>
                <div class="col-12">
                    <i title="Pending" class="fa-light fa-loader"></i>
                    <span>-</span>
                    <span>The order will be delivered today</span>
                </div>
                <div class="col-12">
                    <i title="Ordered" class="fa-light fa-truck"></i>
                    <span>-</span>
                    <span>The order has been placed</span>
                </div>
                <div class="col-12">
                    <i class="fa-light fa-right-left"></i>
                    <span>-</span>
                    <span>When you delete order, the order's quantity will be returned to the current product</span>
                </div>
            </div>
            <table id="ordersTable" class="table table-hover table-sm dataTable no-footer">
                <thead>
                    <tr>
                        <th>
                            <div class="form-check">
                                <input class="form-check-input selectAll" type="checkbox">
                                <label class="form-check-label" for="flexCheckDefault"></label>
                            </div>
                        </th>
                        <th>ID</th>
                        <th>Payment</th>
                        <th>Customer</th>
                        <th>Purchase</th>
                        <th>Amount</th>
                        <th title="Unut price">Unit Price</th>
                        <th title="Discount unit price">Discount unit price</th>
                        <th title="Official price">Official Price</th>
                        <th title="Regular price">Regular price</th>                            
                        <th>Discount</th>
                        <th>Date of sale</th>
                        <th>Expired</th>
                        <th>Payment date</th>
                        <th>Package</th>
                        <th class="text-center">Status</th>
                        <th>Actions</th>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript" src="{{ mix('js/orders/orders.js') }}"></script>

    <script type="text/javascript">
        const ORDER_API_ROUTE = "{{ route('api.orders') }}";
        const EDIT_PRODUCT_ROUTE = "{{ route('purchase.edit', ':id') }}";
        const ORDER_DELETE_ROUTE = "{{ route('order.delete', ':id') }}";
        const ORDER_EDIT_ROUTE = "{{route('order.edit',':id')}}";
        const CUSTOMER_EDIT_ROUTE = "{{ route('customer.edit', ':id') }}";
        const CUSTOMER_API_ROUTE = "{{route('api.customers')}}";
        const PACKAGE = "{{ $package->id }}"
        const PAYMENT_EDIT = "{{ route('payment.edit', [':payment', ':type']) }}";
        const PACKAGE_EDIT_ROUTE = "{{route('package.edit',':id')}}"
        const ORDER_UPDATE_STATUS = "{{route('order.status',':id')}}";
    </script>
@endpush
