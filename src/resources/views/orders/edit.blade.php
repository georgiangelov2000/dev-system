@extends('app')

@section('content')
    <div class="card card-default cardTemplate">
        <div class="card-header">
            <div class="col-12">
                <h3 class="card-title">Edit order</h3>
            </div>
        </div>
        <div class="card-body">

            <div class="col-12">

                <form id="orderForm" action="{{ route('order.update', $order->id) }}" method="PUT">
                    <div class="row flex-wrap">
                        <div class="form-group col-xl-3 col-lg-4 col-md-4 col-sm-4">
                            <label for="customer_id">Customer</label>
                            <select name="customer_id" id="customer_id" class="form-control selectCustomer"
                                data-live-search="true">
                                <option value="{{ $order->customer->id }}">
                                    {{ $order->customer->name }}
                                </option>
                            </select>
                            @error('customer_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-xl-3 col-lg-4 col-md-4 col-sm-4">
                            <label for="user_id">Assign to driver</label>
                            <select id="user_id" name="user_id" class="form-control selectUser" data-live-search="true">
                                <option value="{{ $order->user->id }}">{{ $order->user->username }}</option>
                            </select>
                            <span name="user_id" class="text-danger"></span>
                        </div>

                        <div class="form-group col-xl-3 col-lg-4 col-md-4 col-sm-4">
                            <label for="user_id">Assign to package</label>
                            <select id="package_id" name="package_id" class="form-control selectPackage"
                                data-live-search="true">
                                @if ($order->package)
                                    <option value="{{ $order->package->id }}">
                                        {{ $order->package->package_name }}
                                    </option>
                                @endif
                            </select>
                            <span name="package_id" class="text-danger"></span>
                        </div>

                        <div class="form-group col-xl-3 col-lg-4 col-md-4 col-sm-4">
                            <label>Date of sale:</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="far fa-calendar-alt"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control datepicker" name="date_of_sale"
                                    value="{{ date('m/d/Y', strtotime($order->date_of_sale)) }}">
                            </div>
                            @error('date_of_sale')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            {{-- <p class="input-group-text col-12">{{ date('m/d/Y', strtotime($order->date_of_sale)) }}</p> --}}
                        </div>
                    </div>
                    <div class="row table-responsive">
                        <table class="table table-hover table-sm productOrderTable ">
                            <thead>
                                <th>ID</th>
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
                    <button id="update" type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript" src="{{ mix('js/orders/form.js') }}"></script>
    <script type="text/javascript">
        const DATE_OF_SALE = "{{ $order->date_of_sale }}";
        const ORDER = "{{ $order->id }}";
        const CUSTOMER_API_ROUTE = "{{ route('api.customers') }}"
        const PRODUCT_API_ROUTE = "{{ route('api.products') }}"
        const USER_API_ROUTE = "{{ route('api.users') }}";
        const PACKAGE_API_ROUTE = "{{ route('api.packages') }}";
        const ORDER_API_ROUTE = "{{ route('api.orders') }}";
        const PURCHASE_ROUTE = "{{ route('purchase.edit',':id') }}"
    </script>
@endpush
