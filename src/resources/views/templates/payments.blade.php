<div class="card col-12 cardTemplate p-0">
    <div class="p-3 mb-3">

        <div class="row">
            <div class="col-12">
                <h4>
                    {{ $data->customer->name }}
                    <small class="float-right">{{ $data->date_range }}</small>
                </h4>
            </div>

        </div>

        <div class="row invoice-info mb-3">
            <div class="col-sm-4 invoice-col">
                <span class="font-weight-bold">Address:</span> {{ $data->customer->address }}<br>
                <span class="font-weight-bold">Phone:</span> {{ $data->customer->phone }}<br>
                <span class="font-weight-bold">Email:</span> {{ $data->customer->email }}
            </div>

            <div class="col-sm-4 invoice-col">
                <span class="font-weight-bold">Country:</span> {{ $data->customer->country->name }}<br>
                <span class="font-weight-bold">City:</span> {{ $data->customer->state->name }}<br>
                <span class="font-weight-bold">Zip code:</span> {{ $data->customer->zip }}<br>
            </div>
        </div>


        <div class="row">
            <div class="col-12 table-responsive">
                <table id="paymentsTable" class="table table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Tracking number</th>
                            <th>Invoice number</th>
                            <th>Name</th>
                            <th>Total price</th>
                            <th>Single price</th>
                            <th>Quantity</th>
                            <th>Discount</th>
                            <th>Date of sale</th>
                            <th>Payment date</th>
                            <th>Delayed</th>
                        </tr>
                    </thead>
                    @php
                        $products = $data->products;
                    @endphp
                    <tbody>
                        @if (count($products))
                            @foreach ($products as $key => $product)
                                <tr>
                                    <td>{{ $product['order']['tracking_number'] }}</td>
                                    <td>{{ $product['order']['invoice_number'] }}</td>
                                    <td>{{ $product['order']['product']['name'] }}</td>
                                    <td>€{{$product['price']}}</td>
                                    <td>€{{$product['order']['single_sold_price'] }}</td>
                                    <td>{{ $product['quantity'] }}</td>
                                    <td>{{ $product['order']['discount_percent'] }}%</td>
                                    <td>{{ $product['order']['date_of_sale'] }}</td>
                                    <td>{{ $product['date_of_payment'] }}</td>
                                    <td>{{ $product['delayed_payment'] ?? ''}} days</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

        </div>

        <div class="row justify-content-end">
            <div class="col-6">
                <p class="lead">Amount Due: {{ $data->date_range ? $data->date_range : "Date range is not available" }}</p>
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th>Total discount:</th>
                                <td>{{ $data->total_discount ?? '' }}%</td>
                            </tr>
                            <tr>
                                <th>Regular price:</th>
                                <td>€{{ $data->regular_price ?? '' }}</td>
                            </tr>
                            <tr>
                                <th style="width:50%">Final price:</th>
                                <td>€{{ $data->sum ?? '' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>


        <div class="row no-print">
            <div class="col-12">
                <button type="button" class="btn btn-primary float-right" style="margin-right: 5px;">
                    <i class="fas fa-print"></i> Print
                </button>
                <button type="button" class="btn btn-primary float-right" style="margin-right: 5px;">
                    <i class="fas fa-download"></i> Generate PDF
                </button>
            </div>
        </div>
    </div>
</div>