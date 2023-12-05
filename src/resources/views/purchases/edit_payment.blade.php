@php
    $paymentStatuses = config('statuses.payment_statuses');
    $deliveryStatuses = config('statuses.delivery_statuses');
@endphp

@extends('app')

@section('content')
    <div class="row flex-wrap">
        <div class="card card-default col-12 cardTemplate">
            <div class="card-header bg-primary">
                <div class="col-12">
                    <h3 class="card-title">PAYMENT IDENTIFICATION NUMBER: {{ $payment->id }}</h3>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="col-12 d-flex flex-wrap mt-3">

                    <div class="col-10 d-flex flex-wrap">
                        @if($payment->payment_method === 5)
                            <div class="alert alert-danger" role="alert">
                                The payment has been <a class="alert-link">Returned</a> and the transaction is now closed.
                            </div>
                        @endif
                        <div class="col-12 d-flex flex-wrap p-0">
                            <h5 class="col-12 pt-2"> <i class="fa-light fa-circle-info"></i> Main Information</h5>
                            <div class="col-12">
                                <hr class="mt-0">
                            </div>
                            <div class="form-group col-3">
                                <label for="price">Price</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text border-0"><i class="fa-light fa-coins"></i></span>
                                    </div>
                                    <input disabled type="text" class="form-control border-0"
                                        value="{{ number_format($payment->price, 2, '.', '.') }}" />
                                </div>
                            </div>
                            <div class="form-group col-3">
                                <label for="quantity">Amount</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text border-0">
                                            <i class="fal fa-sort-amount-up"></i>
                                        </span>
                                    </div>
                                    <input disabled type="text" class="form-control border-0"
                                        value="{{ $payment->quantity }}" />
                                </div>
                            </div>
                            <div class="form-group col-3">
                                <label for="">Payment status</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text border-0">
                                            <i class="fa-regular fa-credit-card"></i>
                                        </span>
                                    </div>
                                    <input name="payment_status" disabled type="text" class="form-control border-0"
                                        value="{{ $paymentStatuses[$payment->payment_status] }}" />
                                </div>
                            </div>
                            <div class="form-group col-3">
                                <label for="">Delivery status</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text border-0">
                                            <i class="fa-light fa-truck"></i>
                                        </span>
                                    </div>
                                    <input name="delivery_status" disabled type="text" class="form-control border-0"
                                        value="{{ $deliveryStatuses[$payment->delivery_status] }}" />
                                </div>
                            </div>
                        </div>

                        <div class="col-12 d-flex flex-wrap p-0">
                            <h5 class="col-12 pt-2"> <i class="fa-light fa-calendar-days"></i> Expected dates</h5>
                            <div class="col-12">
                                <hr class="mt-0">
                            </div>
                            <div class="form-group col-3">
                                <label for="">Expected date of payment</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text border-0">
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <input disabled type="text" class="form-control border-0"
                                        value="{{ $payment->expected_date_of_payment }}" />
                                </div>
                            </div>
                            <div class="form-group col-3">
                                <label for="">Expected delivery date</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text border-0">
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <input disabled type="text" class="form-control border-0"
                                        value="{{ $payment->expected_delivery_date }}" />
                                </div>
                            </div>
                        </div>

                        <div class="col-12 d-flex flex-wrap p-0">
                            <h5 class="col-12 pt-2"> <i class="fa-light fa-file-invoice"></i> Invoice details</h5>
                            <div class="col-12">
                                <hr class="mt-0">
                            </div>
                            <div class="form-group col-3">
                                <label for="">Invoice number</label>
                                <div class="input-group">
                                    <input disabled type="text" class="form-control border-0"
                                        value="{{ $payment->invoice->invoice_number }}" />
                                </div>
                            </div>
                            <div class="form-group col-3">
                                <label>Invoice date </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text border-0">
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <input 
                                        disabled 
                                        type="text" 
                                        class="form-control border-0"
                                        value="{{ $payment->invoice->invoice_date }}" 
                                    />
                                </div>
                            </div>
                        </div>

                        @if ($payment->payment_status !== 5)
                            <form action="{{ route('payment.update', [$payment->id, 'purchase']) }}" class="col-12"
                                method="POST">
                                @method('PUT')
                                @csrf

                                <div class="col-12 d-flex flex-wrap p-0">
                                    <h5 class="col-12 pt-2"> <i class="fa-light fa-pen-to-square"></i> Editable fields
                                    </h5>
                                    <div class="col-12">
                                        <hr class="mt-0">
                                    </div>
                                    @if ($payment->delivery_status !== 5)
                                        <div class="form-group col-3">
                                            <label for="is_it_delivered">Delivered</label>
                                            <select class="form-control" name="is_it_delivered" id="is_it_delivered">
                                                @foreach (config('statuses.is_it_delivered') as $key => $val)
                                                    <option
                                                        {{ $key == $payment->purchase->is_it_delivered ? 'selected' : '' }}
                                                        value="{{ $key }}">
                                                        {{ $val }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('is_it_delivered')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif
                                    <div class="form-group col-3">
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
                                    <div class="form-group col-3">
                                        <label for="payment_reference">Payment reference</label>
                                        <input class="form-control" name="payment_reference" type="text"
                                            value="{{ $payment->payment_reference }}" />
                                        @error('payment_reference')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-3">
                                        <label for="notes">Notes</label>
                                        <textarea class="form-control" name="notes" id="notes" cols="2" rows="1"></textarea>
                                        @error('notes')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 p-0 d-flex flex-wrap" id="deliveryWrapper"></div>

                                <div class="form-group col-12">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        @endif

                    </div>

                    <div class="col-2">
                        <img src="{{ $payment->purchase->image_path }}" alt="" class="rounded w-100 m-0">
                    </div>
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
            const paymentStatus = "{{ $payment->payment_status }}";
        </script>
        <script type="text/javascript" src="{{ mix('js/payments/form.js') }}"></script>
    @endpush
@endsection
