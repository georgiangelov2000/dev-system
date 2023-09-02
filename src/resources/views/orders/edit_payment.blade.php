@extends('app')

@section('content')
    <div class="row flex-wrap">
        <div class="card card-default col-5 cardTemplate mr-2">
            <div class="card-header">
                <div class="col-12">
                    <h3 class="card-title">Payment</h3>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="col-12 d-flex flex-wrap">
                    <div class="col-12 mt-4">
                        <div class="col-12">
                            <h6 class="font-weight-bold">Order Status Details:</h6>
                        </div>
                        <div class="col-12">
                            <i class="fa-light fa-exclamation-circle" title="Payment Overdue"></i>
                            <span class="legend-label"><b>Payment Overdue:</b></span>
                            <span>If the payment date is later than the sale date, there's a delay in payment, and the order
                                is marked as "Overdue." Please ensure timely payment to avoid processing delays.</span>
                        </div>
                        <div class="col-12">
                            <i class="fa-light fa-exclamation-circle" title="Mark as Paid"></i>
                            <span class="legend-label"><b>Mark as Paid:</b></span>
                            <span>If your payment date is on time or earlier than the sale date, and the payment is
                                successful, the order will be marked as "Paid." This means your payment has been received,
                                and your order is being processed for delivery.</span>
                        </div>
                        <div class="col-12">
                            <i class="fa-light fa-exclamation-circle" title="Mark as Not Paid"></i>
                            <span class="legend-label"><b>Mark as Not Paid:</b></span>
                            <span>If your payment status is "Pending," it means your payment has not been made yet. The
                                order will be marked as "Not Paid," and processing will not proceed until the payment is
                                completed.</span>
                        </div>
                        <div class="col-12">
                            <i class="fa-light fa-info-circle" title="Mark with Custom Status"></i>
                            <span class="legend-label"><b>Mark with Custom Status:</b></span>
                            <span>If your payment status is "Refunded," it indicates a custom status for your order. The
                                order will be marked accordingly, and if you have any specific refund-related queries,
                                please contact our support team.</span>
                        </div>
                        <div class="col-12">
                            <i class="fa-light fa-info-circle" title="Mark with Custom Status"></i>
                            <span class="legend-label"><b>Mark with Custom Status:</b></span>
                            <span>If your payment status is "Partially Paid," it corresponds to another custom status for
                                your order. The order will be marked accordingly, indicating a partial payment has been
                                received. Please complete the payment to avoid any delays in delivery.</span>
                        </div>
                    </div>
                    <form action="{{ route('payment.update',[$payment->id,'order']) }}" class="col-12" method="POST">
                        @method('PUT')
                        @csrf
                        <div class="form-group col-12">
                            <input type="hidden" name="id" value="{{ $payment->order->id }}">
                        </div>
                        <div class="form-group col-12">
                            <label for="customer_id">Price</label>
                            <input name="price" type="text" class="form-control" max="{{ $payment->price }}"
                                value="{{ $payment->price }}">
                        </div>
                        <div class="form-group col-12">
                            <label for="quantity">Quantity</label>
                            <input name="quantity" type="text" class="form-control" max="{{ $payment->quantity }}"
                                value="{{ $payment->quantity }}">
                            @error('quantity')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-12">
                            <label for="date_of_payment">Date of payment</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="far fa-calendar-alt"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control datepicker" name="date_of_payment"
                                    value="{{ $payment->date_of_payment }}">
                            </div>
                            @error('date_of_payment')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-12">
                            <label for="payment_method">Payment method</label>
                            <select class="form-control" name="payment_method" id="payment_method">
                                <option value="">Select status</option>
                                @foreach (config('statuses.payment_methods_statuses') as $key => $val)
                                    <option {{ $key === $payment->payment_method ? 'selected' : '' }}
                                        value="{{ $key }}">
                                        {{ $val }}
                                    </option>
                                @endforeach
                            </select>
                            @error('payment_method')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-12">
                            <label for="payment_status">Payment status</label>
                            <select class="form-control" name="payment_status" id="payment_status">
                                <option value="">Select status</option>
                                @foreach (config('statuses.payment_statuses') as $key => $val)
                                    <option {{ $key === $payment->payment_status ? 'selected' : '' }}
                                        value="{{ $key }}">
                                        {{ $val }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-12">
                            <label for="payment_reference">Payment reference</label>
                            <input class="form-control" name="payment_reference" type="text"
                                value="{{ $payment->payment_reference }}">
                            @error('payment_reference')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-12">
                            <label for="notes">Notes</label>
                            <textarea class="form-control" name="notes" id="notes" cols="2" rows="3"></textarea>
                            @error('notes')
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
        <div class="card card-default col-6 cardTemplate print-only">
            <div class="card-header">
                <div class="col-12 d-flex align-items-center justify-content-between ">
                    <h3 class="card-title">Details</h3>
                </div>
            </div>
            <div class="card-body">

                <div class="row justify-content-between align-items-center">
                    <div class="col-6">
                        <div class="col-12">
                            <span class="font-weight-bold">Name:</span>
                            <span>{{ $payment->order->customer->name }}</span>
                        </div>
                        <div class="col-12">
                            <span class="font-weight-bold">Email:</span>
                            <span>{{ $payment->order->customer->email }}</span>
                        </div>
                        <div class="col-12">
                            <span class="font-weight-bold">Phone:</span>
                            <span>{{ $payment->order->customer->phone }}</span>
                        </div>
                    </div>
                    <div class="col-6 text-right">
                        @if ($payment->order->customer->image_path)
                            <img class="w-25 m-0"
                                src="{{ $payment->order->customer->image_path  }}" />
                        @endif
                    </div>
                </div>

                <div class="col-12">
                    <h5 class="text-center">PAYMENT RECEIPT </h5>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Payment date</th>
                                <th>Total price</th>
                                <th>Quantity</th>
                                <th>Payment method</th>
                                <th>Payment status</th>
                                <th>Payment reference</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $payment->date_of_payment }}</td>
                                <td>${{ $payment->price }}</td>
                                <td>{{$payment->quantity}}</td>
                                <td>{{ isset(config('statuses.payment_methods_statuses')[$payment->payment_method]) ? config('statuses.payment_methods_statuses')[$payment->payment_method] : '' }}</td>
                                <td>{{ isset(config('statuses.payment_statuses')[$payment->payment_status]) ? config('statuses.payment_statuses')[$payment->payment_status] : '' }}</td>
                                <td>{{ $payment->payment_reference }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="col-12">
                    <h5 class="text-center">ORDER</h5>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Delay</th>
                                <th>Unit Price</th>
                                <th>Total price</th>
                                <th>Regular price</th>
                                <th>Quantity</th>
                                <th>Discount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    {{ $payment->order->purchase->name }}
                                </td>
                                <td>
                                    @php
                                    // Convert the date strings to DateTime objects
                                    $dateOfSale = new DateTime($payment->order->package_extension_date ?: $payment->order->date_of_sale);
                                    $dateOfPayment = new DateTime($payment->date_of_payment);
                                
                                    // Calculate the delay in days (if any)
                                    $delayInterval = $dateOfSale->diff($dateOfPayment);
                                    $delayInDays = $delayInterval->format('%r%a');
                                
                                    // Check if there is a delay in payment
                                    if ($delayInDays > 0) {
                                        $delayMessage = 'Payment is delayed by ' . $delayInDays . ' day(s).';
                                    } else {
                                        $delayMessage = 'Order was paid on time.';
                                    }
                                    @endphp
                                
                                    {{$delayMessage}}
                                </td>                            
                                <td>
                                    ${{ $payment->order->single_sold_price }}
                                </td>
                                <td>
                                    ${{ $payment->order->total_sold_price }}
                                </td>
                                <td>
                                    ${{ $payment->order->original_sold_price }}
                                </td>
                                <td>
                                    {{ $payment->order->sold_quantity }}
                                </td>
                                <td>
                                    {{ $payment->order->discount_percent }}%
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="col-12">
                    <h5 class="text-center">INVOICE</h5>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Invoice number</th>
                                <th>Invoice date</th>
                                <th>Invoice price</th>
                                <th>Invoice quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $payment->invoice->invoice_number }}</td>
                                <td>{{ $payment->invoice->invoice_date }}</td>
                                <td>${{ $payment->invoice->price }}</td>
                                <td>{{ $payment->invoice->quantity }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="row align-items-center">
                    <div class="col-7">
                        <div class="col-12"><b>Company:</b> <span>{{ $settings['name'] }}</span></div>
                        <div class="col-12"><b>Phone number:</b> <span>{{ $settings['phone_number'] }}</span></div>
                        <div class="col-12"><b>Address:</b> <span>{{ $settings['address'] }}</span></div>
                        <div class="col-12"><b>Tax number:</b> <span>{{ $settings['tax_number'] }}</span></div>
                    </div>
                    <div class="col-5 text-right">
                        @if ($settings['image_path'])
                            <img class="w-25 m-0" src="{{ $settings['image_path'] }}" />
                        @endif
                    </div>
                </div>
                <div class="col-12 text-left mt-5">
                    <button id="print" type="button" class="btn btn-primary" style="margin-right: 5px;">
                        <i class="fas fa-download"></i> Generate PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script type="text/javascript">
            $(function() {
                $('select[name="payment_method"],select[name="payment_status"]').selectpicker();

                $('.datepicker').datepicker({
                    format: 'yyyy-mm-dd'
                });

                $('#print').on('click', function() {
                    window.print();
                })

            })
        </script>
    @endpush
@endsection
