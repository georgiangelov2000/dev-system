@extends('app')

@section('content')
    <div class="card card-default cardTemplate">
        <div class="card-body print-only">

            <div class="row">
                <div class="col-12">
                    <h4>
                        <i class="fas fa-globe"></i> Supplier: {{ $purchase->supplier->name }}
                    </h4>
                </div>

            </div>

            <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">
                    From
                    <address>
                        <strong>{{ $purchase->supplier->name }}</strong><br>
                        {{ $purchase->supplier->address }}<br>
                        Phone: {{ $purchase->supplier->phone }}<br>
                        Email: {{ $purchase->supplier->email }}
                    </address>
                </div>

                <div class="col-sm-4 invoice-col">
                    To
                    <address>
                        <strong>{{ $company['name'] }}</strong><br>
                        {{ $company['address'] }}<br>
                        Phone: {{ $company['phone_number'] }}<br>
                        Email: {{ $company['email'] }}
                    </address>
                </div>

                <div class="col-sm-4 invoice-col">
                    <b>Invoice: {{ $purchase->payment->invoice->invoice_number }}</b><br>
                    <br>
                    <b>Purchase ID:</b> {{ $purchase->id }}<br>
                    <b>Payment Due:</b> {{ $purchase->payment->expected_date_of_payment  }}<br>
                </div>

            </div>


            <div class="row">
                <div class="col-12 table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                               <th>Purchase</th>
                                <th>Tracking number</th>
                                <th>Qty</th>
                                <th>Unit price</th>
                                <th>Unit Discount price</th>
                                <th>Total price</th>
                                <th>Weight</th>
                                <th>Height</th>
                                <th>Discount %</th>
                                <th>Category</th>
                                <th>Brand</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    {{ $purchase->name }}
                                </td>
                                <td>
                                    {{ $purchase->code }}
                                </td>
                                <td>
                                    {{$purchase->payment->quantity }}
                                </td>
                                <td>
                                    {{ number_format($purchase->price, 2, '.', '.') }}
                                </td>
                                <td>
                                    {{ number_format($purchase->discount_price, 2, '.', '.') }}
                                </td>
                                <td>
                                    {{ number_format($purchase->payment->price, 2, '.', '.') }}
                                </td>
                                <td>
                                    {{ $purchase->weight }}
                                </td>
                                <td>
                                    {{ $purchase->height }}
                                </td>
                                <td>
                                    {{ $purchase->discount_percent }}
                                </td>
                                <td>
                                    {{ implode(', ', $purchase->categories->pluck('name')->toArray()) }}
                                </td>
                                <td>
                                    {{ implode(', ', $purchase->brands->pluck('name')->toArray()) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>

            <div class="row">

                <div class="col-6">
                    <p class="lead">Details:</p>
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th style="width:50%">Payment Methods:</th>
                                    <td>{{ $purchase->payment->payment_method }}</td>
                                </tr>
                                <tr>
                                    <th>Date of Payment:</th>
                                    <td>{{ $purchase->payment->date_of_payment }}</td>
                                </tr>
                                <tr>
                                    <th>Delivery Date:</th>
                                    <td>{{ $purchase->delivery_date }}</td>
                                </tr>
                                <tr>
                                    <th>Payment status:</th>
                                    <td>{{ $purchase->payment->payment_status }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-6">
                    <p class="lead">Amount Due</p>
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th style="width:50%">Subtotal:</th>
                                    <td>{{ number_format($purchase->total_price, 2, '.', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Unit discount price:</th>
                                    <td>{{ number_format($purchase->discount_price, 2, '.', '.')}}</td>
                                </tr>
                                <tr>
                                    <th>Unit price:</th>
                                    <td>{{ number_format($purchase->price, 2, '.', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Qty:</th>
                                    <td>{{ $purchase->initial_quantity }}</td>
                                </tr>
                                <tr>
                                    <th>Discount:</th>
                                    <td>{{ $purchase->discount_percent }} %</td>
                                </tr>
                                @if($purchase->payment->payment_reference)
                                    <tr>
                                        <th>Payment reference:</th>
                                        <td>{{ $purchase->payment->payment_reference}}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>


            <div class="row no-print">
                <div class="col-12">
                    <a id="printButton" rel="noopener" target="_blank" class="btn btn-primary float-right"><i
                            class="fas fa-print"></i> Print</a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('printButton').addEventListener('click', function() {
            window.print();
        });
    </script>
    @endpush
@endsection
