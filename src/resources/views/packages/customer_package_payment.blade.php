@extends('app')

@section('content')
    <div class="row">
        <div class="card col-12 cardTemplate">
            <div class="card-header d-flex align-items-center p-2">
                <div class="col-10">
                    <h3 class="card-title">Package payment</h3>
                </div>
            </div>
            <div class="card-body">

                <div class="row">
                    <div class="col-12">
                        <p class="bg-dark p-2 font-weight-bold filters">
                            <i class="fa-solid fa-filter"></i> Filters
                        </p>
                    </div>
                    <div class="form-group col-3">
                        <label for="customer_id">Customer</label>
                        <select title="Select customer" class="form-control" name="customer_id" id="customer_id">
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-3">
                        <label for="package_id">Package</label>
                        <select title="" class="form-control" name="package_id" id="package_id"></select>
                    </div>
                    <div class="form-group col-3">
                        <div class="col mb-2">
                            <label></label>
                        </div>
                        <button type="button" class="btn btn-primary" id="search">
                            Show orders <i class="fa-light fa-magnifying-glass"></i>
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <form class="form-row" id="packagePayment" method="post" action="{{route('order.store.payment')}}">
                            @csrf
                            <div class="col-12">
                                <table id="orders" class="table table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th>
                                                <div class="form-check">
                                                    <input class="form-check-input selectAll" type="checkbox">
                                                    <label class="form-check-label" for="flexCheckDefault"></label>
                                                </div>
                                            </th>
                                            <th>
                                                ID
                                            </th>
                                            <th>Name</th>
                                            <th>Tracking number</th>
                                            <th>Single price</th>
                                            <th>Total price</th>
                                            <th>Official price</th>
                                            <th>Quantity</th>
                                            <th>Status</th>
                                            <th>Date of payment</th>
                                            <th>Date of sale</th>
                                            <th>Extension date</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="col-12 submitWrapper"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script type="text/javascript" src="{{ mix('js/packages/customer_package_payment.js') }}"></script>
        <script>
            const PACKAGE_API_ROUTE = "{{ route('api.packages') }}";
            const ORDER_API_ROUTE = "{{ route('api.orders') }}";
            const STORE_ORDER_PACKAGE_PAYMENT = "{{ route('order.store.payment') }}";
            const PRODUCT_EDIT_ROUTE = "{{ route('order.edit',':id') }}"
        </script>
    @endpush
@endsection
