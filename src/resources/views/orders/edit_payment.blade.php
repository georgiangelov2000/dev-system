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
                    <form action="" class="col-12" method="POST">
                        @method('PUT')
                        @csrf
                  
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
                        <span></span>
                    </div>
                    <div class="col-12">
                        <span class="font-weight-bold">Email:</span>
                        <span></span>
                    </div>
                    <div class="col-12">
                        <span class="font-weight-bold">Phone:</span>
                        <span></span>
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
                                    <b></b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span>Total price:</span>
                                    <b>$</b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span>Payment method:</span>
                                    <b>
                                    </b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span>Payment status:</span>
                                    <b>
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
