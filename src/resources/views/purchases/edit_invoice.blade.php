@extends('app')

@section('content')
    <div class="row flex-wrap">
        <div class="card card-default col-5 cardTemplate mr-2">
            <div class="card-header">
                <div class="col-12">
                    <h3 class="card-title">Invoice for {{ $invoice->purchasePayment->purchase->name }}</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="col-12 d-flex flex-wrap">
                    <form action="" class="col-12">
                        @csrf
                        <div class="form-group col-12">
                            <label for="invoice_number">Invoice number</label>
                            <input type="text" id="invoice_number" class="form-control"
                                value="{{ $invoice->invoice_number }}">
                        </div>
                        <div class="form-group col-12">
                            <label for="price">Price</label>
                            <input type="text" class="form-control" id="price" value="{{ $invoice->price }}">
                        </div>
                        <div class="form-group col-12">
                            <label for="quantity">Quantity</label>
                            <input type="number" class="form-control" id="quantity" value="{{ $invoice->quantity }}">
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
                                    <span>Purchase:</span>
                                    <a href="{{route('purchase.edit',$invoice->purchasePayment->purchase->id)}}">{{ $invoice->purchasePayment->purchase->name }}</a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span>Quantity:</span>
                                    <b>{{ $invoice->purchasePayment->quantity }}</b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span>Price:</span>
                                    <b>${{ $invoice->purchasePayment->price }}</b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span>Payment method:</span>
                                    <b>
                                        {{ isset(config('statuses.payment_methods_statuses')[$invoice->purchasePayment->payment_method]) ? config('statuses.payment_methods_statuses')[$invoice->purchasePayment->payment_method] : '' }}
                                    </b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span>Payment status:</span>
                                    <b>
                                        {{ isset(config('statuses.payment_statuses')[$invoice->purchasePayment->payment_status]) ? config('statuses.payment_statuses')[$invoice->purchasePayment->payment_status] : '' }}
                                    </b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span>Payment reference:</span>
                                    <b>
                                        {{ $invoice->purchasePayment->payment_reference }}
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
