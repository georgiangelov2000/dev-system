@php
    $paymentStatuses = config('statuses.payment_statuses');
    $deliveryStatuses = config('statuses.delivery_statuses');
@endphp

@extends('app')

@section('content')
    <div class="row flex-wrap">
        <div class="card card-default col-5 cardTemplate mr-1">
            <div class="card-header bg-primary">
                <div class="col-12">
                    <h3 class="card-title">Payment</h3>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="col-12 d-flex flex-wrap">
                    <form action="{{ route('payment.update', [$payment->id, 'purchase']) }}" class="col-12" method="POST">
                        @method('PUT')
                        @csrf
                        <div class="form-group col-12">
                            <input type="hidden" name="id" value="{{ $payment->purchase->id }}">
                        </div>
                        <div class="form-group col-12">
                            <label for="price">Price</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa-light fa-coins"></i></span>
                                </div>
                                <input 
                                    disabled 
                                    type="text"
                                    class="form-control"
                                    value="{{ $payment->price }}" 
                                />
                            </div>
                        </div>
                        <div class="form-group col-12">
                            <label for="quantity">Amount</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fal fa-sort-amount-up"></i></span>
                                </div>
                                <input 
                                    disabled 
                                    type="text" 
                                    class="form-control" 
                                    value="{{ $payment->quantity }}"
                                />
                            </div>
                        </div>
                        <div class="form-group col-12">
                            <label for="">Expected date of payment</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                                <input 
                                    disabled 
                                    type="text" 
                                    class="form-control"
                                    value="{{ $payment->expected_date_of_payment }}" 
                                />
                            </div>
                        </div>
                        <div class="form-group col-12">
                            <label for="">Expected delivery date</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                                <input 
                                    disabled 
                                    type="text" 
                                    class="form-control"
                                    value="{{ $payment->expected_delivery_date }}" 
                                />
                            </div>
                        </div>
                        <div class="form-group col-12">
                            <label for="">Payment status</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa-regular fa-credit-card"></i>
                                    </span>
                                </div>
                                <input name="payment_status" disabled type="text" class="form-control"
                                value="{{ $paymentStatuses[$payment->payment_status] }}" />
                            </div>
                        </div>
                        <div class="form-group col-12">
                            <label for="">Delivery status</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa-light fa-truck"></i>
                                    </span>
                                </div>
                                <input 
                                    name="delivery_status" 
                                    disabled 
                                    type="text" 
                                    class="form-control"
                                    value="{{ $deliveryStatuses[$payment->delivery_status] }}" 
                                />
                            </div>
                        </div>
                        <div class="form-group col-12">
                            <label for="is_it_delivered">Delivered</label>
                            <select class="form-control" name="is_it_delivered" id="is_it_delivered">
                                @foreach (config('statuses.is_it_delivered') as $key => $val)
                                    <option {{ $key == $payment->purchase->is_it_delivered ? 'selected' : '' }} value="{{ $key }}">
                                        {{ $val }}
                                    </option>
                                @endforeach
                            </select>
                            @error('is_it_delivered')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div id="deliveryWrapper"></div>
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
                            <label for="payment_reference">Payment reference</label>
                            <input 
                                class="form-control" 
                                name="payment_reference" 
                                type="text"
                                value="{{ $payment->payment_reference }}"
                            />
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
            <div class="card-header bg-primary">
                <div class="col-12">
                    <h3 class="card-title">Details</h3>
                </div>
            </div>
            <div class="card-body">

                <div class="row justify-content-between align-items-center">
                    <div class="col-6">
                        <div class="col-12">
                            <span class="font-weight-bold">Name:</span>
                            <span>{{ $payment->purchase->supplier->name }}</span>
                        </div>
                        <div class="col-12">
                            <span class="font-weight-bold">Email:</span>
                            <span>{{ $payment->purchase->supplier->email }}</span>
                        </div>
                        <div class="col-12">
                            <span class="font-weight-bold">Phone:</span>
                            <span>{{ $payment->purchase->supplier->phone }}</span>
                        </div>
                    </div>
                    <div class="col-6 text-right">
                        @if ($payment->purchase->supplier->image_path)
                            <img class="w-25 m-0" src="{{ $payment->purchase->supplier->image_path }}" />
                        @endif
                    </div>
                </div>

                <div class="col-12">
                    <h5 class="text-center">PAYMENT RECEIPT</h5>
                    <table class="table table-hover">
                        <thead class="bg-primary rounded-left rounded-right">
                            <tr>
                                <th class="rounded-left border-0 text-center">Payment date</th>
                                <th class="border-0 text-center">Final price</th>
                                <th class="border-0 text-center">Amount</th>
                                <th class="border-0 text-center">Payment method</th>
                                <th class="border-0 text-center">Payment status</th>
                                <th class="border-0 text-center rounded-right">Payment reference</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border-left-0 border-top-0 border-right text-center">{{ $payment->date_of_payment }}</td>
                                <td class="border-left-0 border-top-0 border-right text-center">{{ number_format($payment->price, 2, '.', '.') }}</td>
                                <td class="border-left-0 border-top-0 border-right text-center">{{ $payment->quantity }}</td>
                                <td class="border-left-0 border-top-0 border-right text-center">
                                    {{ isset(config('statuses.payment_methods_statuses')[$payment->payment_method]) ? config('statuses.payment_methods_statuses')[$payment->payment_method] : '' }}
                                </td>
                                <td class="border-left-0 border-top-0 border-right text-center">
                                    {{ isset(config('statuses.payment_statuses')[$payment->payment_status]) ? config('statuses.payment_statuses')[$payment->payment_status] : '' }}
                                </td>
                                <td class="border-left-0 border-top-0 text-center">
                                    {{ $payment->payment_reference }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="col-12">
                    <h5 class="text-center">PURCHASE</h5>
                    <table class="table table-hover">
                        <thead class="bg-primary rounded-left rounded-right">
                            <tr>
                                <th class="rounded-left border-0 text-center">ID</th>
                                <th class="border-0 text-center">Product</th>
                                <th class="border-0 text-center">Single price</th>
                                <th class="border-0 text-center">Final price</th>
                                <th class="border-0 text-center">Amount</th>
                                <th class="border-0 text-center">Discount %</th>
                                <th class="border-0 text-center">Category</th>
                                <th class="border-0 text-center rounded-right">Tracking number</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border-left-0 border-top-0 border-right text-center">
                                   {{ $payment->purchase->id }}
                                </td>
                                <td class="border-left-0 border-top-0 border-right text-center">
                                    {{ $payment->purchase->name }}
                                </td>
                                <td class="border-left-0 border-top-0 border-right text-center">
                                    {{ number_format($payment->purchase->price, 2, '.', '.') }}
                                </td>
                                <td class="border-left-0 border-top-0 border-right text-center">
                                    {{ number_format($payment->purchase->total_price, 2, '.', '.') }}
                                </td>
                                <td class="border-left-0 border-top-0 border-right text-center">
                                    {{ $payment->purchase->initial_quantity }}
                                </td>
                                <td class="border-left-0 border-top-0 border-right text-center">
                                    {{ $payment->purchase->discount_percent }}
                                </td>
                                <td class="border-left-0 border-top-0 border-right text-center">
                                    {{ $payment->purchase->categories->first()->name }}
                                </td>
                                <td class="border-left-0 border-top-0 text-center">
                                    {{ $payment->purchase->code }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="col-12">
                    <h5 class="text-center">INVOICE</h5>
                    <table class="table table-hover">
                        <thead class="bg-primary rounded-left rounded-right">
                            <tr>
                                <th class="rounded-left border-0 text-center">Invoice number</th>
                                <th class="border-0 text-center">Invoice date</th>
                                <th class="border-0 text-center">Invoice price</th>
                                <th class="border-0 text-center rounded-right">Invoice amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border-left-0 border-top-0 border-right text-center">{{ $payment->invoice->invoice_number }}</td>
                                <td class="border-left-0 border-top-0 border-right text-center">{{ $payment->invoice->invoice_date }}</td>
                                <td class="border-left-0 border-top-0 border-right text-center">{{ number_format($payment->invoice->price, 2, '.', '.') }}</td>
                                <td class="border-left-0 border-top-0 text-center">{{ $payment->invoice->quantity }}</td>
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

                <div class="col-6 text-left mt-5">
                    <button id="print" type="button" class="btn btn-primary" style="margin-right: 5px;">
                        <i class="fas fa-download"></i> Generate PDF
                    </button>
                </div>

            </div>
        </div>
    </div>
    @push('scripts')
        <script type="text/javascript">
            const expectedDateOfPayment = new Date("{{ date('Y-m-d', strtotime($payment->expected_date_of_payment)) }}");
            const expectedDeliveryDate = new Date("{{ date('Y-m-d', strtotime($payment->expected_delivery_date)) }}");
            const dateOfPayment = "{{ date('Y-m-d', strtotime($payment->date_of_payment)) }}";
            const deliveryDate = "{{ date('Y-m-d', strtotime($payment->purchase->delivery_date)) }}";
        </script>
        <script type="text/javascript" src="{{ mix('js/payments/form.js') }}"></script>
    @endpush
@endsection
