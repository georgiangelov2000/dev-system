@extends('app')

@section('content')
    <div class="card card-default cardTemplate">
        <div class="card-header">
            <div class="col-12">
                <h3 class="card-title">Mass Edit purchases for {{ $supplier->name }}</h3>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-12">
                    <h6 class="font-weight-bold">Legend:</h6>
                </div>
                <div class="col-12">
                    <span>- You can update purchases with status delivered</span>
                </div>
            </div>
            <form method="PUT" onsubmit="updatePurchases(event)">
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
                        <th>Price</th>
                        <th>Discount price</th>
                        <th>Total price</th>
                        <th>Regular price</th>
                        <th>Quantity</th>
                        <th>Init.quantity</th>
                        <th>Discount</th>
                        <th>Categories</th>
                        <th>Sub categories</th>
                        <th>Brands</th>
                        <th class="text-center">Status</th>
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
                            <input 
                                type="text" 
                                class="form-control" 
                                name="price" 
                                id="price"
                                placeholder="Enter a numeric value (e.g., 1.00)"
                            />
                        </div>
                        <div class="form-group col-2">
                            <label for="quantity">Quantity</label>
                            <input 
                                type="number" 
                                class="form-control" 
                                name="quantity"
                                id="quantity"
                                min="0"
                                placeholder="Enter a integer value (e.g.,1,2)"
                            />
                        </div>
                        <div class="form-group col-2">
                            <label for="quantity">Discount%</label>
                            <input 
                                type="number" 
                                class="form-control" 
                                name="discount_percent"
                                id="discount_percent"
                                placeholder="Enter a integer value (e.g.,1,2)"
                            />
                        </div>
                        <div class="form-group col-2">
                            <label for="category_id">Categories</label>
                            <select class="form-control" name="category_id" id="category_id">
                                <option value="9999">Please select</option>
                                @foreach ($categories as $category)
                                    <option name="category_id" value="{{$category->id}}">
                                        {{$category->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-2">
                            <label for="sub_category_ids">Sub categories</label>
                            <select name="sub_category_ids" id="sub_category_ids" class="form-control" multiple data-selected-text-format="count > 1"></select>
                        </div>
                        <div class="form-group col-2">
                            <label for="category_id">Brands</label>
                            <select class="form-control" name="brand_id" id="brand_id" multiple data-selected-text-format="count > 1">
                                <option value="">Please select</option>
                                @foreach ($brands as $brand)
                                    <option value="{{$brand->id}}">
                                        {{$brand->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-2">
                            <button id="massEditSubmit" class="btn btn-primary w-100">Save changes</button>
                        </div>
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
        const PURCHASE_UPDATE = "{{route('purchase.mass.update')}}";
        const SUB_CATEGORY_API_ROUTE = "{{route('api.subcategories')}}";
        const CONFIG_URL = "{{config('app.url')}}";
    </script>
@endpush
