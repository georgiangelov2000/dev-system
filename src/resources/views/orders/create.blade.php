@extends('app')

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

                        <div class="form-group col-3 mb-0">
                            <label for="customer_id">Customer</label>
                            <select id="customer_id" name="customer_id" class="form-control selectCustomer"
                                data-live-search="true">
                            </select>
                            <span name="customer_id" class="text-danger"></span>
                        </div>

                        <div class="form-group col-3 mb-0">
                            <label>Date of sale:</label>
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

                        <div class="form-group col-3 mb-0">
                            <label for="order_status">Tracking number</label>
                            <div class="input-group mb-3">
                                <input 
                                    type="text" 
                                    name="tracking_number" 
                                    class="form-control"
                                    placeholder="Enter or generate tracking number"
                                >
                                <span class="input-group-append">
                                    <button 
                                        type="button" 
                                        id="generateCode" 
                                        class="btn btn-primary btn-flat"
                                    >Generate</button>
                                </span>
                            </div>
                            <span name="tracking_number" class="text-danger"></span>
                        </div>

                        <div class="form-group col-12">
                            <label for="">Search purchase</label>
                            <select name="" id="" class="productFilter form-control" data-live-search="true">
                            </select>
                        </div>

                    </div>

                    <table class="table table-hover table-sm productOrderTable ">
                        <thead>
                            <th>Actions</th>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Unit price</th>
                            <th>Qty</th>
                            <th>Category</th>
                            <th>Sub categories</th>
                            <th>Brands</th>
                            <th>Order qty</th>
                            <th>Order unit price</th>
                            <th>Order discount %</th>
                            <th>Order original price</th>
                            <th>Order regular price</th>
                        </thead>
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
        const ORDER_INDEX_ROUTE = "{{route('order.index')}}";
        const CUSTOMER_API_ROUTE = "{{ route('api.customers') }}";
        const PRODUCT_API_ROUTE = "{{ route('api.products') }}";
    </script>
@endpush
