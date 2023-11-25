@extends('app')

@section('content')
    <div class="card card-default cardTemplate">
        <div class="card-header bg-primary">
            <div class="col-12">
                <h3 class="card-title">Orders for {{ $purchase->name }}</h3>
            </div>
        </div>
        <div class="card-body">
                <div class="row">
                    <div class="form-group col-xl-2 col-lg-3 col-md-3 col-sm-3">
                        <label for="customRange1">Expected delivery date</label>
                        <input type="text" class="form-control pull-right" name="datetimes" data-search="expected_delivery_date" />
                    </div>
                    <div class="form-group col-xl-2 col-lg-3 col-md-3 col-sm-3">
                        <label for="customRange1">Delivery date</label>
                        <input type="text" class="form-control pull-right" name="datetimes" data-search="expected_delivery_date" />
                    </div>
                    <div class="form-group col-xl-2 col-lg-3 col-md-3 col-sm-3 mb-0">
                        <label for="">Status</label>
                        <select name="status" id="" class="form-control selectType" multiple>
                            @foreach (config('statuses.payment_statuses') as $key => $status)
                                <option value="{{ $key }}">{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            <table id="ordersTable" class="table table-hover table-sm dataTable no-footer table-responsive">
                <thead>
                    <th></th>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Purchase</th>
                    <th>Track.numer</th>
                    <th>Amount</th>
                    <th>Unit Price</th>
                    <th>Official Price</th>
                    <th>Regular price</th>
                    <th>Discount</th>
                    <th>Delivered</th>
                    <th>Exp delivery date</th>
                    <th>Delivery date</th>
                    <th>Delivery delay</th>
                    <th>Package</th>
                    <th>Delivery Status</th>
                    <th>Actions</th>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript" src="{{ mix('js/orders/orders.js') }}"></script>
    <script type="text/javascript">
        const PRODUCT_ID = "{{$purchase->id}}"
        const ORDER_API_ROUTE = "{{route('api.orders')}}";
        const CUSTOMER_EDIT_ROUTE = "{{route('customer.edit',':id')}}"
        const PAYMENT_EDIT = "{{ route('payment.edit', [':payment', ':type']) }}";
        const EDIT_PRODUCT_ROUTE = "{{ route('purchases.edit', ':id') }}";
        const PACKAGE_EDIT_ROUTE = "{{route('package.edit',':id')}}";
        const ORDER_UPDATE_STATUS = "{{route('order.status',':id')}}";
        const ORDER_EDIT_ROUTE = "{{route('order.edit',':id')}}";
        const ORDER_DELETE_ROUTE = "{{route('order.delete',':id')}}";
    </script>
@endpush
