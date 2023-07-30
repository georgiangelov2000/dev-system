@extends('app')

@section('content')
    <div class="row">
        <div class="card col-12 cardTemplate">
            <div class="card-header">
                <div class="col-12">
                    <h3 class="card-title">
                        Mass edit orders for {{ $customer->name }}
                    </h3>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-warning alert-dismissible col-10">
                    <h5><i class="icon fas fa-ban"></i> Alert!</h5>
                    For orders with statuses such as Overdue, Paid, Refunded,Pending, Partially Paid, you will not be able
                    to make changes. These orders already have payment records and are marked as paid
                </div>

                <form id="massUpdateOrders" method="POST" action="{{ route('order.mass.update') }}">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">

                    <table id="ordersTable" class="table table-hover table-sm dataTable no-footer">
                        <thead>
                            <tr>
                                <th>
                                    <div class="form-check">
                                        <input class="form-check-input selectAll" type="checkbox">
                                        <label class="form-check-label" for="flexCheckDefault"></label>
                                    </div>
                                </th>
                                <th>ID</th>
                                <th>Payment</th>
                                <th>Customer</th>
                                <th>Product</th>
                                <th title="Quantity">Qty</th>
                                <th title="Single price">Unit Price</th>
                                <th title="Discount unit price">Disc.unit price</th>
                                <th title="Official price">Official Price</th>
                                <th title="Original price">Orig. Price</th>
                                <th>Discount</th>
                                <th>Date of sale</th>
                                <th>Expired</th>
                                <th>Delay Payment</th>
                                <th>Payment date</th>
                                <th>Package</th>
                                <th class="text-center">Status</th>
                                <th>Paid</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>

                    <div class="row">
                        <div class="form-group col-12 mb-0">
                            <h6>Options</h6>
                            <hr class="mt-2 mb-2">
                        </div>
                        <div class="col-12 d-flex flex-wrap p-0">
                            <div class="form-group col-2">
                                <label for="price">Price</label>
                                <input type="text" class="form-control" name="price" id="price">
                            </div>
                            <div class="form-group col-2">
                                <label for="discount_percent">Discount %</label>
                                <input type="number" class="form-control" name="discount_percent" id="discount_percent">
                            </div>
                            <div class="form-group col-2">
                                <label for="sold_quantity">Quantity</label>
                                <input type="number" class="form-control" name="sold_quantity" id="sold_quantity">
                            </div>
                            <div class="form-group col-2">
                                <label for="package_id">Packages</label>
                                <select class="form-control" name="package_id" id="package_id">
                                    <option value="">Please select</option>
                                    @foreach ($packages as $package)
                                        <option value="{{$package->id}}">
                                            {{$package->name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-2">
                                <label for="date_of_sale">Date of sale</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <input 
                                        type="text" 
                                        class="form-control float-right datepicker"
                                        name="date_of_sale"
                                        data-date-format="mm/dd/yyyy">
                                </div>
                            </div>
                        </div>
                    </div>

                    <button id="massUpdateButton" class="btn btn-primary" type="submit">
                        Save changes
                    </button>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')
        <script type="text/javascript" src="{{ mix('js/orders/orders.js') }}"></script>
        <script type="text/javascript">
            const ORDER_API_ROUTE = "{{ route('api.orders') }}";
            const CUSTOMER = "{{ $customer->id }}";
            const CUSTOMER_API_ROUTE = "{{ route('api.customers') }}";
            const ORDER_UPDATE_STATUS = "{{ route('order.status', ':id') }}";
            const ORDER_DELETE_ROUTE = "{{ route('order.delete', ':id') }}";
            const ORDER_EDIT_ROUTE = "{{ route('order.edit', ':id') }}";
            const EDIT_PRODUCT_ROUTE = "{{ route('purchase.edit', ':id') }}";
            const CUSTOMER_EDIT_ROUTE = "{{ route('customer.edit', ':id') }}";
            const PACKAGE_EDIT_ROUTE = "{{ route('package.edit', ':id') }}"
            const PAYMENT_API = "{{ route('payment.edit.order', ':id') }}"
            const STATUS = [6, 7, 8];
            const IS_PAID = 0;
        </script>
    @endpush
@endsection
