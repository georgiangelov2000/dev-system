@extends('app')
@section('title', 'Edit order')

@section('content')
    <div class="card card-default cardTemplate">
        <div class="card-header">
            <div class="col-12">
                <h3 class="card-title">Edit order</h3>
            </div>
        </div>
        <div class="card-body">

            <div class="col-12">

                <form action="{{ route('order.update',$currentOrder->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row flex-wrap">

                        <div class="form-group col-3">
                            <label for="">Customer</label>
                            <select name="customer_id" id="" class="form-control selectCustomer"
                                data-live-search="true">
                                <option value="{{ $currentOrder->customer->id }}">
                                    {{ $currentOrder->customer->name }}
                                </option>
                            </select>
                            @error('customer_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-3">
                            <label>Date order:</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="far fa-calendar-alt"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control datepicker" name="date_of_sale">
                            </div>
                            @error('date_of_sale')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-3">
                            <label for="">Order status</label>
                            <select name="status" id="" class="form-control selectType">
                                @foreach (config('statuses.order_statuses') as $key => $status)
                                    <option {{ $currentOrder->status === $key ? 'selected' : '' }}
                                        value="{{ $key }}">{{ $status }}</option>
                                @endforeach
                            </select>
                            @error('status')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-3">
                            <label for="order_status">Tracking code</label>
                            <div class="input-group mb-3">
                                <input type="text" name="tracking_number" value="{{$currentOrder->tracking_number}}" class="form-control rounded-0">
                                <span class="input-group-append">
                                    <button type="button" id="generateCode" class="btn btn-info btn-flat">Generate</button>
                                </span>
                            </div>
                        </div>

                    </div>

                    <table class="table table-hover productOrderTable ">
                        <thead>
                            <th></th>
                            <th>Invoice number</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Single price (markup)</th>
                            <th>Total order price (markup)</th>
                            <th>Discount %</th>
                            <th>Available quantity</th>
                            <th>Single purchase price</th>
                            <th>Total price</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td data-id="{{ $currentOrder->product->id }}">
                                    <input type="hidden" value="{{ $currentOrder->product->id }}" name="product_id[]" />
                                </td>
                                <td>
                                    <input 
                                        type="text" 
                                        class="form-control form-control-sm"
                                        value="{{ $currentOrder->invoice_number }}" name="invoice_number[]" />
                                </td>
                                <td>
                                    {{ $currentOrder->product->name }}
                                </td>
                                <td>
                                    <div class="form-group col-12">
                                        <input 
                                            name="sold_quantity[]" 
                                            type='number'
                                            max='{{ $currentOrder->product->quantity }}'
                                            class='form-control form-control-sm orderQuantity' 
                                            value="{{$currentOrder->sold_quantity}}"
                                            onkeyup="handleOrderQuantity(this)" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group col-12">
                                        <input 
                                            type='text' 
                                            name="single_sold_price[]"
                                            class='form-control form-control-sm orderSinglePrice'
                                            value="{{ $currentOrder->single_sold_price }}"
                                            onkeyup="handleSinglePrice(this)" />
                                    </div>
                                </td>
                                <td>
                                    <input 
                                        type='hidden' 
                                        name="total_sold_price[]"
                                        value="{{ $currentOrder->total_sold_price }}" />
                                    <span class="totalOrderPrice">{{ $currentOrder->total_sold_price }}</span>
                                </td>
                                <td>
                                    <div class="form-group col-12">
                                        <input 
                                            type='text' 
                                            value="{{$currentOrder->discount_percent}}" 
                                            class='form-control form-control-sm'
                                            name="discount_percent[]" 
                                            onkeyup="handleDiscountChange(this)" 
                                        />
                                    </div>
                                </td>
                                <td class="purchaseQuantity">
                                    {{$currentOrder->product->quantity}}
                                </td>
                                <td>
                                    {{$currentOrder->product->price}}
                                </td>
                                <td class="totalPrice">
                                    {{$currentOrder->product->total_price}}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
    <script type="text/javascript" src="{{ mix('js/orders/form.js') }}"></script>
    <script type="text/javascript">
        let DATE_OF_SALE = "{{$currentOrder->date_of_sale}}";
        let CUSTOMER_API_ROUTE = "{{ route('api.customers') }}"
        let PRODUCT_API_ROUTE = "{{ route('api.products') }}"
    </script>
@endpush
