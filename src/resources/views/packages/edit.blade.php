@extends('app')

@section('content')
    <div class="card card-default cardTemplate">

        <div class="card-header">
            <div class="col-12">
                <h3 class="card-title">Edit package</h3>
            </div>
        </div>

        <div class="card-body">
            <div class="col-12">
                <form action='{{ route('package.update', $package->id) }}' method='POST'>
                    @csrf
                    @method('PUT')

                    <div class="row flex-wrap">

                        <div class="form-group col-3">
                            <label for="package_name">Package Name</label>
                            <input type="text" name="package_name" placeholder="Enter package name" class="form-control"
                                value="{{ $package->package_name }}" />
                            @error('package_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-3">
                            <label for="tracking_number">Tracking number</label>
                            <input type="text" name="tracking_number" placeholder="Tracking number" id="tracking_number"
                                class="form-control" value="{{ $package->tracking_number }}" />
                            @error('tracking_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-3">
                            <label for="customer_id">Customer</label>
                            <select name="customer_id" id="customer_id" class="form-control selectCustomer"
                                data-live-search="true">
                            </select>
                            @error('customer_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-3">
                            <label for="package_type">Package type</label>
                            <select id="package_type" class="form-control packageType" name="package_type"
                                title="Choose one of the following...">
                                @foreach (config('statuses.package_types') as $key => $item)
                                    <option {{ $package->package_type === $key ? 'selected' : '' }}
                                        value="{{ $key }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-3 mb-0">
                            <label for="delivery_method">Delivery method</label>
                            <select id="delivery_method" class="form-control deliveryMethod" name="delivery_method"
                                title="Choose one of the following...">
                                @foreach (config('statuses.delivery_methods') as $key => $item)
                                    <option {{ $package->delivery_method === $key ? 'selected' : '' }}
                                        value="{{ $key }}">{{ $item }}</option>
                                @endforeach
                            </select>
                            <small id="emailHelp" class="form-text text-muted">
                                Field indicating the method of delivery (e.g. ground, air, sea, etc.)
                            </small>
                        </div>

                        <div class="form-group col-3 mb-0">
                            <label>Expected delivery date</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="far fa-calendar-alt"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control datepicker" name="expected_delivery_date"
                                    value="{{ $package->expected_delivery_date }}">
                                <small id="emailHelp" class="form-text text-muted">
                                    When the delivery date for a package is interited, the purchase date will be
                                    automatically adjusted to reflect the new delivery date
                                </small>
                            </div>
                            @error('delivery_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-3 mb-0">
                            <label for="package_notes">Package notes</label>
                            <textarea name="package_notes" class="form-control" id="package_notes" cols="3" rows="3" maxlength="255">{{ $package->package_notes }}</textarea>
                            <small id="emailHelp" class="form-text text-muted">
                                Field for staff members to include any notes or special instructions related to the package
                            </small>
                        </div>

                        <div class="form-group col-3 mb-0">
                            <label for="customer_notes">Customer notes</label>
                            <textarea name="customer_notes" class="form-control" id="customer_notes" cols="3" rows="3" maxlength="255">{{ $package->customer_notes }}</textarea>
                            <small id="emailHelp" class="form-text text-muted">
                                Field for customers to include any notes or special requests related to the package
                            </small>
                        </div>

                        <div class="form-group col-12">
                            <label for="">Search purchase</label>
                            <select id="" class="form-control purchaseFilter"></select>
                        </div>

                    </div>

                    <table class="table table-striped table-hover productOrderTable ">
                        <thead>
                            <tr>
                                <th>Actions</th>
                                <th>ID</th>
                                <th>Tracking number</th>
                                <th>Name</th>
                                <th>Date of sale</th>
                                <th>Single price</th>
                                <th>Official price</th>
                                <th>Original price</th>
                                <th>Discount</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($package->orders as $order)
                                <tr name="package">
                                    <td>
                                        @if ($order->status !== 1 && $order->is_paid !== 1)
                                            <button onclick='removeRow(this)' class='btn p-0'>
                                                <i class='fa-light fa-trash text-danger'></i>
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $order->id }}
                                        <input type="hidden" name="order_id[]" value="{{ $order->id }}">
                                    </td>
                                    <td>
                                        {{ $order->tracking_number }}
                                    </td>
                                    <td>
                                        {{ $order->purchase->name }}
                                    </td>
                                    <td>
                                        {{ $order->date_of_sale }}
                                    </td>
                                    <td name="single-sold-price">
                                        €{{ $order->single_sold_price }}
                                    </td>
                                    <td name="total-sold-price">
                                        €{{ $order->total_sold_price }}
                                    </td>
                                    <td>
                                        €{{ $order->original_sold_price }}                                        
                                    </td>
                                    <td>
                                        {{$order->discount_percent}}%
                                    </td>
                                    <td>
                                        {{$order->sold_quantity}}
                                    </td>
                                    {{-- <td>
                                        @if($order->status === 1 && $order->is_paid === 1 && $order->orderPayments)
                                            <span class="text-success">Yes</span>
                                        @endif
                                    </td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <button class="btn btn-primary" type="submit">Submit</button>

                </form>

                <div class="row">
                    <div class="col-md-12">
                        <div class="cardTemplate mt-2 mb-2">
                            <div class="card-footer rounded bg-white p-0">
                                <div class="row">
                                    <div class="col-sm-6 col-6">
                                        <div class="description-block border-right">
                                            <h5 class="description-header ordersCount">
                                                0
                                            </h5>
                                            <span class="description-text">Purchases</span>
                                        </div>

                                    </div>
                                    <div class="col-sm-6 col-6">
                                        <div class="description-block border-right">
                                            <h5 class="description-header packagePrice">0</h5>
                                            <span class="description-text">Price</span>
                                        </div>

                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script type="text/javascript" src="{{ mix('js/packages/form.js') }}"></script>
    <script type="text/javascript">
        let CUSTOMER_API_ROUTE = "{{ route('api.customers') }}"
        let ORDER_API_ROUTE = "{{ route('api.orders') }}"
        let CUSTOMER = "{{ $package->customer_id }}"
    </script>
@endpush
