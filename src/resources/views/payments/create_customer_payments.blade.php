@extends('app')

@section('content')
    <div class="row">
        <div class="card col-12 cardTemplate">
            <div class="card-header">
                <div class="col-12">
                    <h3 class="card-title">Create payment</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="row flex-wrap">
                    <div class="col-6">
                        <div class="row mb-3">
                            <div class="col-12">
                                <h6 class="font-weight-bold">Legend:</h6>
                            </div>
                            <div class="col-12">
                                <i class="fa-light fa-user" aria-hidden="true"></i>
                                <span>-</span>
                                <span>Select the customer for whom you want to receive orders</span>
                            </div>
                            <div class="col-12">
                                <i class="fa-thin fa-calendar-days"></i>
                                <span>-</span>
                                <span>The date range period will find orders where the sale date is in this range</span>
                            </div>
                            <div class="col-12">
                                <i class="fa-light fa-bookmark"></i>
                                <span>-</span>
                                <span>Select order which you want to make a payment record</span>
                            </div>
                            <div class="col-12">
                                <i class="fa-light fa-triangle-exclamation"></i>
                                <span>-</span>
                                <span>Please be careful when you enter your data in the form</span>
                            </div>
                            <div class="col-12">
                                <i class="fa-light fa-circle-info"></i>
                                <span>-</span>
                                <span>You can filter only orders which they are not paid</span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-6">
                                <label for="customer_id">Customer</label>
                                <select name="customer_id" class="form-control selectCustomer"
                                    data-live-search="true"></select>
                            </div>

                            <div class="form-group col-6">
                                <label>Date range</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control datepicker" name="date_of_sale">
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="order_id">Orders</label>
                                <select name="order_id" id="order_id" class="form-control">

                                </select>
                            </div>
                            <div class="form-group col-12">
                                <button title="Filter" class="btn btn-primary filter" type="button">
                                    Search order <i class="fa-light fa-magnifying-glass"></i>
                                </button>
                            </div>
                        </div>
                        <div id="loader" class="spinner-border text-dark" role="status" style="display: none;">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <div id="orderOverview" class="col-6 d-none">
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script type="text/javascript" src="{{ mix('js/customers/payments.js') }}"></script>
        <script type="text/javascript">
            const CUSTOMER_SEARCH_URL = "{{ route('customer.edit', ':id') }}";
            const CUSTOMER_API_ROUTE = "{{ route('api.customers') }}";
            const SEARCH_ORDER = "{{ route('api.orders') }}";
            const ORDER_PAYMENT = "{{route('order.store.payment')}}"
        </script>
    @endpush

@endsection
