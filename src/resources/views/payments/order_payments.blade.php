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

    {{-- Invoice modal --}}
    <div class="modal fade" id="modalInvoice" tabindex="-1" role="dialog" aria-labelledby="modalInvoice"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Invoice</h5>
                    <button type="button" class="close modalCloseBtn" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        @csrf
                        @method('PUT')
                        <div class='modal-form'>
                            <input type="hidden" name="order_payment_id" value="">
                            <div class="form-group">
                                <label id="invoice_number" class="required">Invoice number</label>
                                <input type='text' name='invoice_number' id='invoice_number'
                                    value='' required class='form-control' />
                                <span name="invoice_number" id="invoice_number" class="text-danger"> </span>
                            </div>
                            <div class="form-group">
                                <label id="price" class="required">Price</label>
                                <input type='text' name='price' id='price' value='' required
                                    class='form-control' />
                                <span name="price" id="price" class="text-danger"> </span>
                            </div>
                            <div class="form-group">
                                <label id="quantity" class="required">Quantity</label>
                                <input type='text' name='quantity' id='quantity' value='' required
                                    class='form-control' />
                                <span name="quantity" id="quantity" class="text-danger"> </span>
                            </div>
                            <div class="form-group">
                                <label for="invoice_date">Invoice date</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control datepicker" name="invoice_date"
                                        value="">
                                </div>
                                <span name="invoice_date" id="invoice_date" class="text-danger"> </span>
                            </div>
                        </div>
                    </form>
                </div>


                <div class="modal-footer">
                    <div class='row w-100'>
                        <div class='col-12 p-0'>
                            <button type="button" class="btn btn-secondary modalCloseBtn"
                                data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="submitForm">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script type="text/javascript" src="{{ mix('js/payments/customer_payments.js') }}"></script>
        <script type="text/javascript">
            const ORDER_PAYMENT_API = "{{ route('api.payments') }}";
            const TYPE = "order";
            const ORDER_EDIT_ROUTE = "{{ route('order.edit', ':id') }}";
            const ORDER_PAYMENT_EDIT_ROUTE = "{{ route('payment.edit', [':payment', ':type']) }}";
            const ORDER_PAYMENT_DELETE_ROUTE = "{{ route('payment.delete', [':payment', ':type']) }}";
            const ORDER_INVOICE_API_ROUTE = "{{route('api.order.invoices')}}"
            const ORDER_INVOICE_UPDATE_ROUTE = "{{route('invoice.update.order',':id')}}"
        </script>
    @endpush
@endsection
