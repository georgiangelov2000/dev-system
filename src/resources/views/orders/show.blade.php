@extends('app')

@section('content')
    <div class="card card-default cardTemplate">
        <div class="card-body print-only">

            <div class="row">
                <div class="col-12">
                    <h4>
                        <i class="fas fa-globe"></i> Customer: {{ $order->customer->name }}
                    </h4>
                </div>

            </div>

            <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">
                    From
                    <address>
                        <strong>{{ $order->customer->name }}</strong><br>
                        {{ $order->customer->address }}<br>
                        Phone: {{ $order->customer->phone }}<br>
                        Email: {{ $order->customer->email }}
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
                    <b>Invoice: {{ $order->payment->invoice->invoice_number }}</b><br>
                    <b>Purchase ID:</b> {{ $order->id }}<br>
                    <b>Payment Due:</b> {{ $order->payment->expected_date_of_payment  }}<br>
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
                                    {{ $order->purchase->name }}
                                </td>
                                <td>
                                    {{ $order->tracking_number }}
                                </td>
                                <td>
                                    {{$order->payment->quantity }}
                                </td>
                                <td>
                                    {{ number_format($order->single_sold_price, 2, '.', '.') }}
                                </td>
                                <td>
                                    {{ number_format($order->discount_single_sold_price, 2, '.', '.') }}
                                </td>
                                <td>
                                    {{ number_format($order->payment->price, 2, '.', '.') }}
                                </td>
                                <td>
                                    {{ $order->purchase->weight }}
                                </td>
                                <td>
                                    {{ $order->purchase->height }}
                                </td>
                                <td>
                                    {{ $order->discount_percent }}
                                </td>
                                <td>
                                    {{ implode(', ', $order->purchase->categories->pluck('name')->toArray()) }}
                                </td>
                                <td>
                                    {{ implode(', ', $order->purchase->brands->pluck('name')->toArray()) }}
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
                                    <td>{{ $order->payment->payment_method }}</td>
                                </tr>
                                <tr>
                                    <th>Date of Payment:</th>
                                    <td>{{ $order->payment->date_of_payment }}</td>
                                </tr>
                                <tr>
                                    <th>Delivery Date:</th>
                                    <td>{{ $order->delivery_date }}</td>
                                </tr>
                                <tr>
                                    <th>Payment status:</th>
                                    <td>{{ $order->payment->payment_status }}</td>
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
                                    <td>{{ number_format($order->payment->price, 2, '.', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Unit discount price:</th>
                                    <td>{{ number_format($order->discount_single_sold_price, 2, '.', '.')}}</td>
                                </tr>
                                <tr>
                                    <th>Unit price:</th>
                                    <td>{{ number_format($order->single_sold_price, 2, '.', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Qty:</th>
                                    <td>{{ $order->payment->quantity }}</td>
                                </tr>
                                <tr>
                                    <th>Discount:</th>
                                    <td>{{ $order->discount_percent }} %</td>
                                </tr>
                                @if($order->payment->payment_reference)
                                    <tr>
                                        <th>Payment reference:</th>
                                        <td>{{ $order->payment->payment_reference}}</td>
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
