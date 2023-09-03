@extends('app')

@section('content')
    <div class="card card-default cardTemplate">
        <div class="card-header">
            <div class="col-12">
                <h3 class="card-title">Orders for {{ $purchase->name }}</h3>
            </div>
        </div>
        <div class="card-body">
                <div class="row">
                    <div class="form-group col-3">
                        <label for="customRange1">Date of sale</label>
                        <input type="text" class="form-control pull-right" name="date_of_sale" />
                    </div>
                    <div class="form-group col-3">
                        <label for="customRange1">Date of payment</label>
                        <input type="text" class="form-control pull-right" name="date_of_payment" />
                    </div>
                    <div class="form-group col-3">
                        <label for="">Order status</label>
                        <select name="status" id="" class="form-control selectType" multiple>
                            @foreach (config('statuses.order_statuses') as $key => $status)
                                <option value="{{ $key }}">{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            <table id="ordersTable" class="table table-hover table-sm dataTable no-footer">
                <thead>
                    <th></th>
                    <th>ID</th>
                    <th>Payment</th>
                    <th>Customer</th>
                    <th>Purchase</th>
                    <th title="Quantity">Qty</th>
                    <th title="Unut price">Unit Price</th>
                    <th title="Discount unit price">Disc.unit price</th>
                    <th title="Official price">Official Price</th>
                    <th title="Regular price">Regular price</th>                            
                    <th>Discount</th>
                    <th>Date of sale</th>
                    <th>Expired</th>
                    <th>Delay Payment</th>
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
        const PRODUCT_ID = "{{$purchase->id}}"
        const ORDER_API_ROUTE = "{{route('api.orders')}}";
        const CUSTOMER_EDIT_ROUTE = "{{route('customer.edit',':id')}}"
        const PAYMENT_EDIT = "{{ route('payment.edit', [':payment', ':type']) }}";
        const EDIT_PRODUCT_ROUTE = "{{ route('purchase.edit', ':id') }}";
        const PACKAGE_EDIT_ROUTE = "{{route('package.edit',':id')}}";
        const ORDER_UPDATE_STATUS = "{{route('order.status',':id')}}";
        const ORDER_EDIT_ROUTE = "{{route('order.edit',':id')}}";
        const ORDER_DELETE_ROUTE = "{{route('order.delete',':id')}}";
    </script>
@endpush
