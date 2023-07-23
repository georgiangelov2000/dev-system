@extends('app')

@section('content')
    <div class="row flex-wrap">
        <div class="card card-default col-5 cardTemplate mr-2">
            <div class="card-header">
                <div class="col-12">
                    <h3 class="card-title">Invoice for order ID: {{ $invoice->orderPayment->id }}</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="col-12 d-flex flex-wrap">
                    <form action="{{ route('invoice.order.update', $invoice->id) }}" class="col-12" method="POST">
                        @csrf
                        @method('PUT')

                        <input type="hidden" value="{{ $invoice->orderPayment->id }}" name="order_payment_id">

                        <div class="form-group col-12">
                            <label for="invoice_number">Invoice number</label>
                            <input type="text" name="invoice_number" id="invoice_number" class="form-control"
                                value="{{ $invoice->invoice_number }}">
                            @error('invoice_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-12">
                            <label for="price">Price</label>
                            <input type="text" class="form-control" name="price" id="price"
                                value="{{ $invoice->price }}">
                            @error('price')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-12">
                            <label for="quantity">Quantity</label>
                            <input type="number" class="form-control" name="quantity" id="quantity"
                                value="{{ $invoice->quantity }}">
                            @error('quantity')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-12">
                            <label for="invoice_date">Invoice date</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="far fa-calendar-alt"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control datepicker" name="invoice_date"
                                    value="{{ $invoice->invoice_date }}">
                            </div>
                            @error('invoice_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-12">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card card-default col-5 cardTemplate">
            <div class="card-header">
                <div class="col-12">
                    <h3 class="card-title">Details</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="col-12">
                    <h5 class="text-center">
                        PAYMENT RECEIPT
                    </h5>
                    <table class="table table-hover">
                        <tbody>
                            <tr>
                                <td>
                                    <span>Order:</span>
                                    <a
                                        href="{{ route('order.edit', $invoice->orderPayment->order->id) }}">{{ $invoice->orderPayment->order->id }}</a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span>Quantity:</span>
                                    <b>{{ $invoice->orderPayment->quantity }}</b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span>Price:</span>
                                    <b>${{ $invoice->orderPayment->price }}</b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span>Payment method:</span>
                                    <b>
                                        {{ isset(config('statuses.payment_methods_statuses')[$invoice->orderPayment->payment_method]) ? config('statuses.payment_methods_statuses')[$invoice->orderPayment->payment_method] : '' }}
                                    </b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span>Payment status:</span>
                                    <b>
                                        {{ isset(config('statuses.payment_statuses')[$invoice->orderPayment->payment_status]) ? config('statuses.payment_statuses')[$invoice->orderPayment->payment_status] : '' }}
                                    </b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span>Payment reference:</span>
                                    <b>
                                        {{ $invoice->orderPayment->payment_reference }}
                                    </b>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-12">
                    <h5 class="text-center">
                        INVOICE RECEIPT
                    </h5>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Invoice number</th>
                                <th>Invoice Date</th>
                                <th>Price</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $invoice->invoice_number }}</td>
                                <td>{{ $invoice->invoice_date }}</td>
                                <td>${{ $invoice->price }}</td>
                                <td>{{ $invoice->quantity }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @push('scripts')
            <script type="text/javascript">
                $(function() {
                    $('select[name="status"]').selectpicker();
                    $('.datepicker').datepicker({
                        format: 'yyyy-mm-dd'
                    });
                })
            </script>
        @endpush
    @endsection
