@extends('app')

@section('content')
    <div class="card card-default cardTemplate">

        <div class="card-header">
            <div class="col-12">
                <h3 class="card-title">Create package</h3>
            </div>
        </div>

        <div class="card-body">
            <div class="col-12">
                <form action='{{ route('package.store') }}' method='POST'>
                    @csrf
                    <div class="row flex-wrap">

                        <div class="form-group col-3">
                            <label for="package_name">Package name</label>
                            <input type="text" name="package_name" placeholder="Package name" class="form-control" />
                            @error('package_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-3">
                            <label for="tracking_number">Tracking number</label>
                            <input type="text" name="tracking_number" placeholder="Tracking number" id="tracking_number"
                                class="form-control" />
                            @error('tracking_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-3">
                            <label for="customer_id">Customer</label>
                            <select name="customer_id" id="customer_id" class="form-control selectCustomer"
                                data-live-search="true">
                            </select>
                            @error('customer_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-3">
                            <label for="package_type">Package type</label>
                            <select id="package_type" class="form-control packageType" name="package_type"
                                title="Choose one of the following...">
                                @foreach (config('statuses.package_types') as $key => $item)
                                    <option value="{{ $key }}">{{ $item }}</option>
                                @endforeach
                            </select>
                            @error('package_type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-3 mb-0">
                            <label for="delivery_method">Delivery method</label>
                            <select id="delivery_method" class="form-control deliveryMethod" name="delivery_method"
                                title="Choose one of the following...">
                                @foreach (config('statuses.delivery_methods') as $key => $item)
                                    <option value="{{ $key }}">{{ $item }}</option>
                                @endforeach
                            </select>
                            <small id="emailHelp" class="form-text text-muted">
                                Field indicating the method of delivery (e.g. ground, air, sea, etc.)
                            </small>
                            @error('delivery_method')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-3 mb-0">
                            <label>Expected delivery date</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="far fa-calendar-alt"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control datepicker" name="expected_delivery_date">
                                <small id="emailHelp" class="form-text text-muted">
                                    When the delivery date for a package is interited, the purchase date will be
                                    automatically adjusted to reflect the new delivery date
                                </small>
                            </div>
                            @error('expected_delivery_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                            <div class="form-group col-3 mb-0">
                                <label for="package_notes">Package notes</label>
                                <textarea maxlength="100" name="package_notes" class="form-control" id="package_notes" cols="3" rows="3"></textarea>
                                <small id="emailHelp" class="form-text text-muted">
                                    Field for staff members to include any notes or special instructions related to the package
                                </small>
                                @error('package_notes')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
    
                            <div class="form-group col-3 mb-0">
                                <label for="customer_notes">Customer notes</label>
                                <textarea maxlength="100" 
                                    name="customer_notes" 
                                    class="form-control" 
                                    id="customer_notes" 
                                    cols="3" 
                                    rows="3"
                                > </textarea>
                                <small id="emailHelp" class="form-text text-muted">
                                    Field for customers to include any notes or special requests related to the package
                                </small>
                                @error('customer_notes')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                        <div class="form-group col-12">
                            <label for="purchase_id">Search orders</label>
                            <select id="purchase_id" class="form-control purchaseFilter">
                                <option disabled>Nothing selected</option>
                            </select>
                        </div>

                    </div>

                    <table class="table table-hover table-sm productOrderTable">
                        <thead>
                            <tr>
                                <th>Actions</th>
                                <th>Image</th>
                                <th>Customer</th>
                                <th>Purchase</th>
                                <th title="Quantity">Quantity</th>
                                <th>Unit price</th>
                                <th>Disc unit price</th>
                                <th>Original price</th>
                                <th>Regular price</th>
                                <th>Discount %</th>
                                <th>Date of sale</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>

                    <button class="btn btn-primary" type="submit">Submit</button>

                </form>

                <div class="row">
                    <div class="col-md-12">
                        <div class="cardTemplate mt-2 mb-2">
                            <div class="card-footer rounded bg-white p-0">
                                <div class="row">
                                    <div class="col-sm-6 col-6">
                                        <div class="description-block border-right">
                                            <h5 class="description-header ordersCount">
                                                0
                                            </h5>
                                            <span class="description-text">Purchases</span>
                                        </div>

                                    </div>
                                    <div class="col-sm-6 col-6">
                                        <div class="description-block">
                                            <h5 class="description-header packagePrice">0.00</h5>
                                            <span class="description-text">Price</span>
                                        </div>

                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script type="text/javascript" src="{{ mix('js/packages/form.js') }}"></script>
    <script type="text/javascript">
        let CUSTOMER_API_ROUTE = "{{ route('api.customers') }}"
        let ORDER_API_ROUTE = "{{ route('api.orders') }}"
        let SUPPLIER_API_ROUTE = "{{ route('api.suppliers') }}";
        let ORDER_EDIT_ROUTE = "{{route('order.edit',':id')}}";
    </script>
@endpush
