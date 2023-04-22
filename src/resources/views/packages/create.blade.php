@extends('app')
@section('title', 'Add package')

@section('content')
    <div class="card card-default cardTemplate">

        <div class="card-header">
            <div class="col-12">
                <h3 class="card-title">Create package</h3>
            </div>
        </div>

        <div class="card-body">
            <div class="col-12">
                <form method="POST">
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
                            <select id="package_type" class="form-control packageType" name="package_type" title="Choose one of the following...">
                                @foreach (config('statuses.package_types') as $key => $item)
                                    <option value="{{$key}}">{{$item}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-3 mb-0">
                            <label for="delievery_method">Delievery method</label>
                            <select id="delievery_method" class="form-control delieveryMethod" name="delievery_method" title="Choose one of the following...">
                                @foreach (config('statuses.delievery_methods') as $key => $item)
                                    <option value="{{$key}}">{{$item}}</option>
                                @endforeach
                            </select>
                            <small id="emailHelp" class="form-text text-muted">
                                Field indicating the method of delivery (e.g. ground, air, sea, etc.)
                            </small>
                        </div>

                        <div class="form-group col-3 mb-0">
                            <label for="name">Increase package price</label>
                            <input type="text" name="increase_package_price" placeholder="Increase package price"
                                class="form-control" />
                            <small id="emailHelp" class="form-text text-muted">
                                Increase the price of the package, if package type is <strong>Express</strong>
                            </small>
                        </div>

                        <div class="form-group col-3 mb-0">
                            <label>Expected delievery date</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="far fa-calendar-alt"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control datepicker" name="delievery_date">
                                <small id="emailHelp" class="form-text text-muted">
                                    When the delivery date for a package is interited, the purchase date will be
                                    automatically adjusted to reflect the new delivery date
                                </small>
                            </div>
                            @error('delievery_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-4">
                            <label for="package_notes">Package notes</label>
                            <textarea name="package_notes" class="form-control" id="package_notes" cols="3" rows="3"></textarea>
                            <small id="emailHelp" class="form-text text-muted">
                                Field for staff members to include any notes or special instructions related to the package
                            </small>
                        </div>

                        <div class="form-group col-4">
                            <label for="customer_notes">Customer notes</label>
                            <textarea name="customer_notes" class="form-control" id="customer_notes" cols="3" rows="3"></textarea>
                            <small id="emailHelp" class="form-text text-muted">
                                Field for customers to include any notes or special requests related to the package
                            </small>
                        </div>

                        <div class="form-group col-12">
                            <label for="">Search purchase</label>
                            <select id="" class="form-control purchaseFilter">
                            </select>
                        </div>

                    </div>

                    <table class="table table-striped table-hover productOrderTable ">
                        <thead>
                            <tr>
                                <th>Actions</th>
                                <th>ID</th>
                                <th>Invoice number</th>
                                <th>Name</th>
                                <th>Single sold price</th>
                                <th>Total sold price</th>
                                <th>Sold quantity</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                    
                    <button class="btn btn-primary" type="submit">Submit</button>

                </form>

                <div class="row">
                    <div class="col-12 d-flex justify-content-end">
                        <div class="card col-4 p-0">
                            <div class="card-header pl-2">
                                <h3 class="card-title">Overview</h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="col p-2 border-bottom">
                                    <strong>Customer: </strong>
                                    <span class="customerName">None</span>
                                </div>
                                <div class="col p-2 border-bottom">
                                    <strong>Total purchases: </strong>
                                    <span class="ordersCount">0</span>
                                </div>
                                <div class="col p-2 border-bottom">
                                    <strong>Price:</strong>
                                    <span class="packagePrice">0</span>
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
        let CUSTOMER_API_ROUTE = "{{route('api.customers')}}"
        let ORDER_API_ROUTE = "{{route('api.orders')}}"
    </script>
@endpush
