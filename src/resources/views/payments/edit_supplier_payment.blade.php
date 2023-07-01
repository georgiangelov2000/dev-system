@extends('app')
@section('title', 'Comapny payment')

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
                    <form action="" class="col-12" method="POST">
                        @method('PUT')
                        @csrf
                        <div class="form-group col-12">
                            <label for="customer_id">Price</label>
                            <input type="text" class="form-control" max="{{ $payment->price }}"
                                value="{{ $payment->price }}">
                        </div>
                        <div class="form-group col-12">
                            <label for="customer_id">Quantity</label>
                            <input type="text" class="form-control" max="{{ $payment->quantity }}"
                                value="{{ $payment->quantity }}">
                        </div>
                        <div class="form-group col-12">
                            <label for="payment_method">Payment method</label>
                            <select class="form-control" name="payment_method" id="payment_method">
                                @foreach (config('statuses.payment_methods_statuses') as $key => $val)
                                    <option {{ $key === $payment->payment_method ? 'selected' : '' }}
                                        value="{{ $key }}">
                                        {{ $val }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-12">
                            <label for="payment_status">Payment status</label>
                            <select class="form-control" name="payment_status" id="payment_status">
                                @foreach (config('statuses.payment_statuses') as $key => $val)
                                    <option {{ $key === $payment->payment_status ? 'selected' : '' }}
                                        value="{{ $key }}">
                                        {{ $val }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-12">
                            <label for="payment_status">Payment reference</label>
                            <input class="form-control" type="text" value="{{ $payment->payment_reference }}">
                        </div>
                        <div class="form-group col-12">
                            <label for="notes">Notes</label>
                            <textarea class="form-control" name="notes" id="notes" cols="2" rows="3"></textarea>
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
                        <span>{{$payment->purchase->supplier->name}}</span>
                    </div>
                    <div class="col-12">
                        <span class="font-weight-bold">Email:</span>
                        <span>{{$payment->purchase->supplier->email}}</span>            
                    </div>
                    <div class="col-12">
                        <span class="font-weight-bold">Phone:</span>
                        <span>{{$payment->purchase->supplier->phone}}</span>
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
                                    <b>{{$payment->date_of_payment}}</b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span>Total price:</span>
                                   <b>{{$payment->price}}</b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span>Payment type:</span>
                                   <b>{{  array_key_exists($payment->payment_method,config('statuses.payment_methods_statuses')) ? config('statuses.payment_methods_statuses')[$payment->payment_method] : ''}}</b>
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
                                <td>r$35,00</td>
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
            })
        </script>
    @endpush
@endsection
