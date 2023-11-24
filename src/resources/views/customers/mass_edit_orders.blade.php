@extends('app')

@section('content')
    <div class="row">
        <div class="card col-12 cardTemplate">
            <div class="card-header bg-primary">
                <div class="col-12">
                    <h3 class="card-title">
                        Mass edit orders for {{ $customer->name }}
                    </h3>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-light" role="alert">
                    You have the flexibility to make updates to orders that have not yet been delivered.
                </div>
                <form method="POST" onsubmit="updateOrders(event)">
                    @csrf
                    
                    <div class="row table-responsive">
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
                                    <th>Customer</th>
                                    <th>Driver</th>
                                    <th>Purchase</th>
                                    <th>Tracking number</th>
                                    <th>Amount</th>
                                    <th>Unit Price</th>
                                    <th>Discount unit price</th>
                                    <th>Official Price</th>
                                    <th>Refular price</th>
                                    <th>Discount</th>
                                    <th>Package</th>
                                    <th>Exp delivery date</th>
                                    <th>Delivery delay</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>

                    <div class="row">
                        <div class="form-group col-12 mb-0">
                            <h6>Options</h6>
                            <hr class="mt-2 mb-2">
                        </div>
                        <div class="col-12 d-flex  flex-wrap p-0">
                            <div class="form-group col-xl-2 col-lg-2 col-md-3 col-sm-4">
                                <label for="price">Single price</label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    name="single_sold_price" 
                                    id="single_sold_price" 
                                    placeholder="Enter a numeric value (e.g., 1.00)" 
                                />
                                @error('single_sold_price')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-xl-2 col-lg-2 col-md-3 col-sm-4">
                                <label for="sold_quantity">Amount</label>
                                <input 
                                    type="number" 
                                    class="form-control" 
                                    name="sold_quantity" 
                                    id="sold_quantity" 
                                    min="0" 
                                    placeholder="Enter a integer value (e.g.,1,2)"
                                />
                                @error('sold_quantity')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-xl-2 col-lg-2 col-md-3 col-sm-4">
                                <label for="discount_percent">Discount %</label>
                                <input 
                                    type="number" 
                                    class="form-control" 
                                    name="discount_percent" 
                                    id="discount_percent" 
                                    min="0"  
                                    placeholder="Enter a integer value (e.g.,1,2)"
                                />
                                @error('discount_percent')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-xl-2 col-lg-2 col-md-3 col-sm-4">
                                <label for="package_id">Packages</label>
                                <select class="form-control" name="package_id" id="package_id">
                                    <option value="">Please select</option>
                                    @foreach ($packages as $package)
                                        <option value="{{$package->id}}">
                                            {{$package->package_name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-xl-2 col-lg-2 col-md-3 col-sm-4">
                                <label for="expected_delivery_date">Expected delivery date</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <input 
                                        type="text" 
                                        class="form-control float-right datepicker"
                                        name="expected_delivery_date"
                                        data-date-format="mm/dd/yyyy"
                                    />
                                    @error('expected_delivery_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror                                    
                                </div>
                            </div>
                            <div class="form-group col-xl-2 col-lg-2 col-md-3 col-sm-4">
                                <label for="expected_date_of_payment">Expected payment date</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <input 
                                        type="text" 
                                        class="form-control float-right datepicker"
                                        name="expected_date_of_payment"
                                        data-date-format="mm/dd/yyyy"
                                    />
                                    @error('expected_date_of_payment')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror  
                                </div>
                            </div>
                            <div class="form-group col-xl-2 col-lg-2 col-md-3 col-sm-4">
                                <label for="package_id">Assign to driver</label>
                                <select class="form-control" name="user_id" id="user_id">
                                    <option value="">Please select</option>
                                    @foreach ($drivers as $driver)
                                        <option value="{{$driver->id}}">
                                            {{$driver->username}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-xl-2 col-lg-2 col-md-12 col-sm-12">
                            <button class="btn btn-primary w-100">
                                Save changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')
        <script type="text/javascript" src="{{ mix('js/customers/mass_edit_orders.js') }}"></script>
        <script type="text/javascript">
            const CUSTOMER = "{{ $customer->id }}";
            const USER_EDIT = "{{ route('user.edit',':id') }}"
            const ORDER_API_ROUTE = "{{ route('api.orders') }}";
            const MASS_UPDATE_ORDERS = "{{route('order.mass.update')}}"
            const CUSTOMER_API_ROUTE = "{{ route('api.customers') }}";
            const EDIT_PRODUCT_ROUTE = "{{ route('purchase.edit', ':id') }}";
            const CUSTOMER_EDIT_ROUTE = "{{ route('customer.edit', ':id') }}";
            const PACKAGE_EDIT_ROUTE = "{{ route('package.edit', ':id') }}"
            const PAYMENT_EDIT = "{{ route('payment.edit', [':payment', ':type']) }}";
            const STATUS = [2];
        </script>
    @endpush
@endsection
