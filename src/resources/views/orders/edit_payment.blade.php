@extends('app')

@section('content')
    <div class="row flex-wrap">
        <div class="card card-default col-5 cardTemplate mr-2">
            <div class="card-header">
                <div class="col-12">
                    <h3 class="card-title">Payment</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="col-12 d-flex flex-wrap">
                    <form action="{{ route('payment.update.order', $payment->id) }}" class="col-12" method="POST">
                        @method('PUT')
                        @csrf
                        <div class="form-group col-12">
                            <input type="hidden" name="order_id" value="{{$payment->order->id}}">
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
                                <input 
                                    type="text" 
                                    class="form-control datepicker" 
                                    name="date_of_payment"
                                    value="{{$payment->date_of_payment}}"
                                >
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
        <div class="card card-default col-5 cardTemplate">
            <div class="card-header">
                <div class="col-12">
                    <h3 class="card-title">Details</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="col-12 mb-5">
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
                <div class="col-12">
                    <h5 class="text-center">
                        PAYMENT RECEIPT
                    </h5>
                    <table class="table table-hover">
                        <tbody>
                            <tr>
                                <td>
                                    <span>Payment date:</span>
                                    <b>{{ $payment->date_of_payment }}</b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span>Total price:</span>
                                    <b>${{ $payment->price }}</b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span>Payment method:</span>
                                    <b>
                                        {{ isset(config('statuses.payment_methods_statuses')[$payment->payment_method]) ? config('statuses.payment_methods_statuses')[$payment->payment_method] : '' }}
                                    </b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span>Payment status:</span>
                                    <b>
                                        {{ isset(config('statuses.payment_statuses')[$payment->payment_status]) ? config('statuses.payment_statuses')[$payment->payment_status] : '' }}
                                    </b>
                                </td>
                            </tr>                            
                        </tbody>
                    </table>
                </div>
                <div class="col-12">
                    <h5 class="text-center">PAYMENT FOR</h5>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Invoice number</th>
                                <th>Invoice date</th>
                                <th>Invoice price</th>
                                <th>Payment price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>INV-000014</td>
                                <td>30/06/2023</td>
                                <td>$35,00</td>
                                <td>$35,00</td>
                            </tr>
                        </tbody>
                    </table>
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
            })
        </script>
    @endpush
@endsection
