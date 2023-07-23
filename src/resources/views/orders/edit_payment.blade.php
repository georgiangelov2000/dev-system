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
                    <form action="{{ route('payment.update.order', $payment->id) }}" class="col-12" method="POST">
                        @method('PUT')
                        @csrf
                        <div class="form-group col-12">
                            <input type="hidden" name="order_id" value="{{ $payment->order->id }}">
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
                            @error('payment_status')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
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
                        @if ($payment->order->customer->image)
                            <img class="w-25 m-0" src="{{ $payment->order->customer->image->path . '/' . $payment->order->customer->image->name }}" />
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
                                <th>Payment method</th>
                                <th>Payment status</th>
                                <th>Payment reference</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $payment->date_of_payment }}</td>
                                <td>${{ $payment->price }}</td>
                                <td>{{ isset(config('statuses.payment_methods_statuses')[$payment->payment_method]) ? config('statuses.payment_methods_statuses')[$payment->payment_method] : '' }}
                                </td>
                                <td>{{ isset(config('statuses.payment_statuses')[$payment->payment_status]) ? config('statuses.payment_statuses')[$payment->payment_status] : '' }}
                                </td>
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
                                <th>Single price</th>
                                <th>Total price</th>
                                <th>Original price</th>
                                <th>Quantity</th>
                                <th>Discount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    {{$payment->order->purchase->name}}
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
                        <div class="col-12"><b>Company:</b> <span>{{ $company->name }}</span></div>
                        <div class="col-12"><b>Phone number:</b> <span>{{ $company->phone_number }}</span></div>
                        <div class="col-12"><b>Address:</b> <span>{{ $company->address }}</span></div>
                        <div class="col-12"><b>Tax number:</b> <span>{{ $company->tax_number }}</span></div>
                    </div>
                    <div class="col-5 text-right">
                        @if ($company->image_path)
                            <img class="w-25 m-0" src="{{ $company->image_path }}" />
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
