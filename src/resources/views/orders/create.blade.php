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

                <form id="orderForm" action="{{ route('order.store') }}" method="POST">
                    @csrf

                    <div class="row flex-wrap">

                        <div class="col-3">
                            <label for="">Customer</label>
                            <select name="customer_id" class="form-control selectCustomer"
                                data-live-search="true">
                            </select>
                            <span name="customer_id" class="text-danger"></span>
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
                            <span name="date_of_sale" class="text-danger"></span>
                        </div>

                        <div class="col-3">
                            <label for="order_status">Order status</label>
                            <select name="status" id="order_status" class="form-control selectType">
                                <option value="">Please select</option>
                                @foreach (config('statuses.order_statuses') as $key => $status)
                                    @if ($key !== 1 && $status !== 'Received')
                                        <option value="{{ $key }}">{{ $status }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <span name="status" class="text-danger"></span>
                        </div>

                        <div class="col-3">
                            <label for="order_status">Tracking number</label>
                            <div class="input-group">
                                <input type="text" name="tracking_number" class="form-control rounded-0"
                                    placeholder="Enter or generate tracking code">
                                <span class="input-group-append">
                                    <button type="button" id="generateCode" class="btn btn-info btn-flat">Generate</button>
                                </span>
                            </div>
                            <span name="tracking_number" class="text-danger"></span>
                        </div>

                        <div class="form-group col-12">
                            <label for="">Search product</label>
                            <select name="" id="" class="productFilter" data-live-search="true">
                            </select>
                        </div>

                    </div>

                    <table class="table table-striped table-hover productOrderTable ">
                        <thead>
                            <th>Action</th>
                            <th>Invoice number</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Single price</th>
                            <th>Total order price</th>
                            <th>Discount %</th>
                            <th>Avail. quantity</th>
                            <th>Single price</th>
                            <th>Total price</th>
                        </thead>
                    </table>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="cardTemplate mt-2 mb-2">
                                <div class="card-footer rounded bg-white p-0">
                                    <div class="row">
                                        <div class="col-sm-4 col-6">
                                            <div class="description-block border-right">
                                                <h5 class="description-header" id="totalOrdersPrice">
                                                    0
                                                </h5>
                                                <span class="description-text">Total purchase price</span>
                                            </div>

                                        </div>

                                        <div class="col-sm-4 col-6">
                                            <div class="description-block border-right">
                                                <h5 class="description-header" id="totalOrdersQuantity">
                                                    0
                                                </h5>
                                                <span class="description-text">Total purchase quantity</span>
                                            </div>

                                        </div>
                                        <div class="col-sm-4 col-6">
                                            <div class="description-block">
                                                <h5 class="description-header">0</h5>
                                                <span class="description-text">Total counts</span>
                                            </div>

                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>


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
        const ORDER_INDEX_ROUTE = "{{route('order.index')}}" ;
        const CUSTOMER_API_ROUTE = "{{ route('api.customers') }}"
        const PRODUCT_API_ROUTE = "{{ route('api.products') }}"
    </script>
@endpush
