@extends('app')
@section('title', 'Add order')

@section('content')
    <div class="card card-default cardTemplate">
        <div class="card-header">
            <div class="col-12">
                <h3 class="card-title">Create order</h3>
            </div>
        </div>
        <div class="card-body">

            <div class="col-12">
                
                <form action="{{route('order.store')}}" method="POST">
                    @csrf

                    <div class="row flex-wrap">

                        <div class="col-3">
                            <label for="">Customer</label>
                            <select name="customer_id" id="" class="form-control selectCustomer" data-live-search="true">
                            </select>
                            @error('customer_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
        
                        <div class="col-3">
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
        
                        <div class="col-3">
                            <label for="order_status">Order status</label>
                            <select name="status" id="order_status" class="form-control selectType">
                                <option value="">Please select</option>
                                @foreach ( config('statuses.order_statuses') as $key => $status)
                                    @if($key !== 1 && $status !== 'Received')
                                        <option value="{{$key}}">{{$status}}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('status')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-3">
                            <label for="order_status">Tracking number</label>
                            <div class="input-group mb-3">
                                <input 
                                    type="text" 
                                    name="tracking_number" 
                                    class="form-control rounded-0"
                                    placeholder="Enter or generate tracking code"
                                >
                                <span class="input-group-append">
                                    <button type="button" id="generateCode" class="btn btn-info btn-flat">Generate</button>
                                </span>
                            </div>
                        </div>
        
                        <div class="form-group col-12">
                            <label for="">Search product</label>
                            <select name="" id="" class="productFilter" data-live-search="true">
                                <option value=""></option>
                            </select>
                        </div>
        
                    </div>

                    <table class="table table-hover productOrderTable ">
                        <thead >
                            <th>Action</th>
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
                            <tr id="initalTr">
                                <td colspan="10" class="text-center">
                                    <p class="mb-0">Please add data</p>
                               </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th class="TFOOTquantity">0</th>
                                <th></th>
                                <th class="TFOOTtotalPrice">0</th>
                            </tr>
                        </tfoot>
                    </table>

                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
    <script type="text/javascript" src="{{ mix('js/helpers/render_helpers.js') }}"></script>
    <script type="text/javascript" src="{{ mix('js/orders/form.js') }}"></script>
    <script type="text/javascript">
        let CUSTOMER_API_ROUTE = "{{route('api.customers')}}"
        let PRODUCT_API_ROUTE = "{{route('api.products')}}"
    </script>
@endpush
