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

                <form action="{{ route('order.update', $order->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row flex-wrap">

                        <div class="form-group col-xl-2 col-lg-4 col-md-4 col-sm-4">
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

                        <div class="form-group col-xl-2 col-lg-4 col-md-4 col-sm-4">
                            <label for="user_id">Assign to driver</label>
                            <select id="user_id" name="user_id" class="form-control selectUser" data-live-search="true">
                                <option value="{{ $order->user->id }}">{{ $order->user->username }}</option>
                            </select>
                            <span name="user_id" class="text-danger"></span>
                        </div>

                        <div class="form-group col-xl-2 col-lg-4 col-md-4 col-sm-4">
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
                            @if ($order->status === 6)
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
                            @else
                                <p class="input-group-text col-12">{{ date('m/d/Y', strtotime($order->date_of_sale)) }}</p>
                            @endif
                        </div>

                        <div class="form-group col-xl-3 col-lg-4 col-md-4 col-sm-4">
                            <label for="order_status">Tracking number</label>
                            @if ($order->status === 6)
                                <div class="input-group mb-3">
                                    <input type="text" name="tracking_number" value="{{ $order->tracking_number }}"
                                        class="form-control">
                                    <span class="input-group-append">
                                        <button type="button" id="generateCode"
                                            class="btn btn-primary btn-flat">Generate</button>
                                    </span>
                                </div>
                                @error('tracking_number')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            @else
                                <p class="input-group-text col-12">{{ $order->tracking_number }}</p>
                            @endif
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
                                <th>Category</th>
                                <th>Sub categories</th>
                                <th>Brands</th>
                                <th>Order amount</th>
                                <th>Order unit price</th>
                                <th>Order discount %</th>
                                <th>Order final price</th>
                                <th>Order regular price</th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>
                                        @if ($order->purchase->image_path)
                                            <img class="img-fluid card-widget widget-user w-100 m-0"
                                                src="{{ $order->purchase->image_path }}" />
                                        @endif
                                    </td>
                                    <td>
                                        <input type="hidden" value="{{ $order->purchase->id }}" name="purchase_id" />

                                        <a href="{{ route('purchase.edit', $order->purchase->id) }}">
                                            {{ $order->purchase->name }}
                                        </a>
                                    </td>
                                    <td>
                                        €{{ $order->purchase->price }}
                                    </td>
                                    <td>
                                        {{ $order->purchase->quantity }}
                                    </td>
                                    <td>
                                        @if ($order->purchase->categories)
                                            {{ $order->purchase->categories->first()->name }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($order->purchase->subcategories)
                                            {{ implode(', ', $order->purchase->subcategories->pluck('name')->toArray()) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($order->purchase->brands)
                                            {{ implode(', ', $order->purchase->brands->pluck('name')->toArray()) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($order->status === 6)
                                            <div class="form-group col-12">
                                                <input name="sold_quantity" type='number'
                                                    data-manipulation-name="sold_quantity"
                                                    class='form-control form-control-sm'
                                                    value="{{ $order->sold_quantity }}"
                                                    onkeyup="handleOrderQuantity(this)" />
                                                @error('sold_quantity')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        @else
                                            {{ $order->sold_quantity }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($order->status === 6)
                                            <div class="form-group col-12">
                                                <input type='text' name="single_sold_price"
                                                    data-manipulation-name="single_sold_price"
                                                    class='form-control form-control-sm'
                                                    value="{{ $order->single_sold_price }}"
                                                    onkeyup="handleSinglePrice(this)" />
                                                @error('single_sold_price')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        @else
                                            €{{ $order->single_sold_price }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($order->status === 6)
                                            <div class="form-group col-12">
                                                <input type='text' name="discount_percent"
                                                    data-manipulation-name="discount_percent"
                                                    value="{{ $order->discount_percent }}"
                                                    class='form-control form-control-sm'
                                                    onkeyup="handleDiscountChange(this)" />
                                                @error('discount_percent')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        @else
                                            {{ $order->discount_percent }}
                                        @endif
                                    </td>
                                    <td>
                                        <span name="original_price">
                                            {{ number_format($order->total_sold_price, 2, '.', ',') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span name="regular_price">
                                            {{ number_format($order->original_sold_price, 2, '.', ',') }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript" src="{{ mix('js/orders/form.js') }}"></script>
    <script type="text/javascript">
        const DATE_OF_SALE = "{{ $order->date_of_sale }}";
        const CUSTOMER_API_ROUTE = "{{ route('api.customers') }}"
        const PRODUCT_API_ROUTE = "{{ route('api.products') }}"
        const USER_API_ROUTE = "{{ route('api.users') }}";
        const PACKAGE_API_ROUTE = "{{ route('api.packages') }}";
    </script>
@endpush
