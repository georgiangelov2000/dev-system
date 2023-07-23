@extends('app')

@section('content')
    <div class="row">
        <div class="card col-12 cardTemplate">
            <div class="card-header">
                <div class="col-12">
                    <h3 class="card-title">Create order payments</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <p class="bg-dark p-2 font-weight-bold filters">
                            <i class="fa-solid fa-filter"></i> Filters
                        </p>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Customers</label>
                            <select name="customer_id" class="form-control selectCustomer">
                                <option value="">All</option>
                                @foreach ($customers as $item)
                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-3">
                        <label for="customRange1">Created</label>
                        <input type="text" class="form-control pull-right" name="datetimes" />
                    </div>
                </div>
                <form action="{{route('payment.store.order')}}" id="paymentOrders" class="col-12" method="POST">
                    @csrf

                    <table id="orders" class="table table-hover table-sm">
                        <thead>
                            <th>
                                <div class="form-check">
                                    <input class="form-check-input selectAll" type="checkbox">
                                    <label class="form-check-label" for="flexCheckDefault"></label>
                                </div>
                            </th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Total price</th>
                            <th>Quantity</th>
                            <th>Date of payment</th>
                            <th>Tracking number</th>
                            <th>Package</th>
                            <th>Extension date</th>
                            <th>Date of sale</th>
                            <th>Created</th>
                            <th>Paid</th>
                        </thead>
                    </table>
                    <div class="row submitWrapper"></div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script type="text/javascript" src="{{ mix('js/orders/payments.js') }}"></script>
        <script type="text/javascript">
            const ORDER_API_ROUTE = "{{ route('api.orders') }}";
        </script>
    @endpush

@endsection
