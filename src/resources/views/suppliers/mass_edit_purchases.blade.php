@extends('app')

@section('content')
    <div class="card card-default cardTemplate">
        <div class="card-header bg-primary">
            <div class="col-12">
                <h3 class="card-title">Mass Edit purchases for {{ $supplier->name }}</h3>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-light" role="alert">
                You have the flexibility to make updates to purchases that have not yet been delivered.
            </div>
            <form method="PUT" action="{{route('purchase.mass.update')}}" onsubmit="updatePurchases(event)">
                @csrf
                <table id="massEditPurchases" class="table table-hover table-sm dataTable no-footer">
                    <thead>
                        <th>
                            <div class="form-check">
                                <input class="form-check-input selectAll" type="checkbox">
                                <label class="form-check-label" for="flexCheckDefault"></label>
                            </div>
                        </th>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Unit Price</th>
                        <th>Discount unit price</th>
                        <th>Final price</th>
                        <th>Regular price</th>
                        <th>Amount</th>
                        <th>Initial amount</th>
                        <th>Discount</th>
                        <th>Categories</th>
                        <th>Brands</th>
                        <th>Exp date of payment</th>
                        <th>Exp delivery date</th>
                        <th>Delivery delay</th>
                    </thead>
                </table>

                <div class="row mt-5">
                    <div class="form-group col-12 mb-0">
                        <h6>Options</h6>
                        <hr class="mt-2 mb-2">
                    </div>
                    <div class="col-12 d-flex flex-wrap p-0">
                        <div class="form-group col-2">
                            <label for="price">Single price</label>
                            <input type="text" class="form-control" name="price" id="price"
                                placeholder="Enter a numeric value (e.g., 1.00)" />
                        </div>
                        <div class="form-group col-2">
                            <label for="quantity">Quantity</label>
                            <input type="number" class="form-control" name="quantity" id="quantity" min="0"
                                placeholder="Enter a integer value (e.g.,1,2)" />
                        </div>
                        <div class="form-group col-2">
                            <label for="quantity">Discount%</label>
                            <input type="number" class="form-control" name="discount_percent" id="discount_percent"
                                placeholder="Enter a integer value (e.g.,1,2)" />
                        </div>
                        <div class="form-group col-xl-2 col-lg-2 col-md-3 col-sm-4">
                            <label for="category_id">Categories</label>
                            <select class="form-control" name="categories" id="categories">
                                <option value="9999">Please select</option>
                                @foreach ($categories as $category)
                                    <option name="category_id" value="{{ $category->id }}">
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-xl-2 col-lg-2 col-md-3 col-sm-4">
                            <label for="sub_category_ids">Sub categories</label>
                            <select name="sub_category_ids" id="sub_category_ids" class="form-control" multiple
                                data-selected-text-format="count > 1"></select>
                        </div>
                        <div class="form-group col-xl-2 col-lg-2 col-md-3 col-sm-4">
                            <label for="category_id">Brands</label>
                            <select class="form-control" name="brands" id="brands" multiple
                                data-selected-text-format="count > 1">
                                <option value="">Please select</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}">
                                        {{ $brand->name }}
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
                    </div>
                    <div class="form-group col-xl-2 col-lg-2 col-md-12 col-sm-12">
                        <button class="btn btn-primary w-100">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript" src="{{ mix('js/suppliers/mass_edit_purchases.js') }}"></script>
    <script type="text/javascript">
        const SUPPLIER_ID = {{ "$supplier->id" }};
        const PURCHASE_API = "{{ route('api.products') }}";
        const PURCHASE_EDIT = "{{ route('purchase.edit', ':id') }}";
        const SUB_CATEGORY_API_ROUTE = "{{ route('api.subcategories') }}";
        const CONFIG_URL = "{{ config('app.url') }}";
    </script>
@endpush
