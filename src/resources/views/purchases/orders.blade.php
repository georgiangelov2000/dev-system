@extends('app')
@section('title', 'Product orders')

@section('content')
    <div class="card card-default cardTemplate">
        <div class="card-header">
            <div class="col-12">
                <h3 class="card-title">Orders for {{ $product->name }}</h3>
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
                    <th>
                        <div class="form-check">
                            <input class="form-check-input selectAll" type="checkbox">
                            <label class="form-check-label" for="flexCheckDefault"></label>
                        </div>
                    </th>
                    <th>ID</th>
                    <th>Invoice number</th>
                    <th>Customer</th>
                    <th>Quantity</th>
                    <th>Single price</th>
                    <th>Selling price</th>
                    <th>Original price</th>
                    <th>Discount</th>
                    <th>Expired</th>
                    <th>Created at</th>
                    <th>Date of sale</th>
                    <th>Date of payment</th>
                    <th>Status</th>
                    <th>Paid</th>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript" src="{{ mix('js/purchases/orders.js') }}"></script>
    <script type="text/javascript">
        let PRODUCT_ID = "{{$product->id}}"
        let ORDER_API_ROUTE = "{{route('api.orders')}}";
        let CUSTOMER_EDIT_ROUTE = "{{route('customer.edit',':id')}}"
    </script>
@endpush
