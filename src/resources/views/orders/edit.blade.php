    @extends('app')

@section('content')
    <div class="card card-default cardTemplate">
        <div class="card-header bg-primary">
            <div class="col-12">
                <h3 class="card-title">Edit order</h3>
            </div>
        </div>
        <div class="card-body">
            @if(!$order->is_editable)
                <div class="alert alert-danger d-inline-block" role="alert">
                    The order is currently marked as <a href="#" class="alert-link"> {{ $order->status }}</a>. Please note that certain fields cannot be edited as the purchase is already closed. <a href="#" class="alert-link">Delivery date: {{ $order->payment->delivery_date }}</a>.
                </div>
            @endif
            <div class="col-12">
                <form id="orderForm" action="{{ route('order.update', $order->id) }}" method="PUT">
                    <div class="row flex-wrap">
                        <div class="form-group col-xl-3 col-lg-3 col-md-3 col-sm-3 p-0">
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

                        <div class="form-group col-xl-3 col-lg-3 col-md-3 col-sm-3">
                            <label for="user_id">Assign to driver</label>
                            <select id="user_id" name="user_id" class="form-control selectUser" data-live-search="true">
                                <option value="{{ $order->user->id }}">{{ $order->user->username }}</option>
                            </select>
                            <span name="user_id" class="text-danger"></span>
                        </div>

                        <div class="form-group col-xl-2 col-lg-2 col-md-2 col-sm-2">
                            <label for="user_id">Assign to package</label>
                            @if(!$order->is_editable)
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa-light fa-boxes-packing"></i>
                                        </span>
                                    </div>
                                    <input disabled="" placeholder="{{ $order->package->package_name ?? "Not assigned" }}" class="form-control">
                                </div>
                            @else
                                <select id="package_id" name="package_id" class="form-control selectPackage"
                                data-live-search="true">
                                @if ($order->package)
                                    <option value="{{ $order->package->id }}">
                                        {{ $order->package->package_name }}
                                    </option>
                                @endif
                                </select>
                                <span name="package_id" class="text-danger"></span>
                            @endif
                        </div>

                        <div class="form-group col-xl-2 col-lg-2 col-md-2 col-sm-2">
                            <label class="form-label required">Expected date of payment:</label>
                            @if (!$order->is_editable)
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <input disabled name="expected_date_of_payment" class="form-control" value="{{ $order->payment->expected_date_of_payment }}" />
                                </div>
                            @else
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <input type="text"
                                        value="{{ date('m/d/Y', strtotime($order->payment->expected_date_of_payment))}}"
                                        class="form-control datepicker" 
                                        name="expected_date_of_payment" />
                                </div>
                                @error('expected_date_of_payment')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            @endif
                        </div>

                        <div class="form-group col-xl-2 col-lg-2 col-md-2 col-sm-2">
                            <label class="form-label required">Expected Delivery date:</label>
                            @if(!$order->is_editable)
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <input disabled name="expected_delivery_date" class="form-control" value="{{ $order->expected_delivery_date }}" />
                                </div>
                            @else
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control datepicker" name="expected_delivery_date"
                                        value="{{ date('m/d/Y', strtotime($order->expected_delivery_date)) }}">
                                </div>
                                @error('delivery-date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            @endif
                        </div>
                    </div>

                    <div class="row table-responsive">
                        <table class="table table-hover table-sm productOrderTable ">
                            <thead>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Purchase</th>

                                @if($order->is_editable)
                                    <th>Unit price</th>
                                @endif
                                
                                <th>Amount</th>

                                @if($order->is_editable)
                                    <th>Init.Amount</th>
                                    <th>Order amount</th>
                                    <th>Order unit price</th>
                                    <th>Order discount %</th>
                                    <th>Track.number</th>
                                    <th>Order final price</th>
                                    <th>Order regular price</th>
                                @else
                                    <th>Unit price</th>
                                    <th>Discount unit price</th>
                                    <th>Final price</th>
                                    <th>Regular price</th>
                                    <th>Discount %</th>
                                    <th>Tracking number</th>
                                    <th>Created</th>
                                    <th>Updated</th>
                                @endif
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
        const DELIVERY_DATE = "{{ $order->delivery_date }}";
        
        const ORDER = "{{ $order->id }}";
        const ORDER_AMOUNT = "{{ $order->sum_of_orders_amount }}";
        const ORIGINAL_AMOUNT = "{{ $order->original_amount }}";

        const IS_EDITABLE = "{{ $order->is_editable }}"

        const ORDER_INDEX_ROUTE = "{{ route('order.index') }}";
        
        const CUSTOMER_API_ROUTE = "{{ route('api.customers') }}"
        const PRODUCT_API_ROUTE = "{{ route('api.products') }}"
        const USER_API_ROUTE = "{{ route('api.users') }}";
        const PACKAGE_API_ROUTE = "{{ route('api.packages') }}";
        const ORDER_API_ROUTE = "{{ route('api.orders') }}";
        const PURCHASE_ROUTE = "{{ route('purchase.edit',':id') }}"
    
    </script>
@endpush
