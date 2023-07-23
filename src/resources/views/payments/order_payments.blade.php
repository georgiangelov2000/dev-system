@extends('app')

@section('content')
    <div class="row">
        <div class="card col-12 cardTemplate">
            <div class="card-header">
                <div class="col-12">
                    <h3 class="card-title">Order payments</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="col-3">
                        <label for="customRange1">Select Customer</label>
                        <select class="form-control selectCustomer" name="customer">
                            <option value="0">Nothing selected</option>
                            @foreach ($customers as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-3 dateRange">
                        <label for="customRange1">Date range</label>
                        <div class="d-flex align-items-center">
                            <input type="text" class="form-control pull-right" name="datetimes" />
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="col mb-2">
                            <label></label>
                        </div>
                        <button id="filter" title="Filter" class="btn btn-primary" type="button">
                            <i class="fa-light fa-magnifying-glass"></i>
                        </button>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-12 d-flex mt-2">
                        <label for="">Disabled Date range: </label>
                        <div class="form-check ml-2">
                            <input class="form-check-input disabledDateRange" type="checkbox">
                            <label class="form-check-label"></label>
                        </div>
                    </div>
                    <div id="loader" class="spinner-border text-dark" role="status" style="display: none;">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
        <div id="paymentTemplate" class="card col-12 cardTemplate table-responsive d-none"></div>
    </div>

    @push('scripts')
        <script type="text/javascript" src="{{ mix('js/payments/customer_payments.js') }}"></script>
        <script type="text/javascript">
            const ORDER_PAYMENT_API = "{{route('api.order.payments')}}";
            const ORDER_EDIT_ROUTE = "{{route('order.edit',':id')}}";
            const ORDER_PAYMENT_EDIT_ROUTE = "{{route('payment.edit.order',':id')}}";
            const ORDER_INVOICE_EDIT_ROUTE = "{{route('invoice.order.edit',':id')}}";
        </script>
    @endpush

@endsection
