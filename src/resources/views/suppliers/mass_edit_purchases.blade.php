@extends('app')

@section('content')
    <div class="card card-default cardTemplate">
        <div class="card-header">
            <div class="col-12">
                <h3 class="card-title">Mass Edit purchases for {{ $supplier->name }}</h3>
            </div>
        </div>
        <div class="card-body">
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
                        <th>Unit price</th>
                        <th>Unit disc price</th>
                        <th>Tot.price</th>
                        <th>Orig.price</th>
                        <th>Quantity</th>
                        <th>Initial Quantity</th>
                        <th>Discount</th>
                        <th>Categories</th>
                        <th>Sub categories</th>
                        <th>Brands</th>
                        <th>Paid</th>
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
                            <input type="text" class="form-control" name="price" id="price">
                        </div>
                        <div class="form-group col-2">
                            <label for="quantity">Quantity</label>
                            <input type="number" class="form-control" name="quantity"
                                id="quantity">
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
                            <label for="" class="mb-4"></label>
                            <button id="massEditSubmit" class="btn btn-primary w-100">Submit</button>
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
