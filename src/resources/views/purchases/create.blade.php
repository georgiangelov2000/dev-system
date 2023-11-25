@extends('app')

@section('content')
    <div class="card card-default cardTemplate">
        <div class="card-header bg-primary">
            <div class="col-12">
                <h3 class="card-title">Create purchase</h3>
            </div>
        </div>
        <div class="card-body pb-0">
            <form class="d-flex flex-wrap" action='{{ route('purchases.store') }}' method='POST' enctype="multipart/form-data">
                @csrf
                <div class="row flex-wrap">
                    <div class="col-10 d-flex flex-wrap p-2">
                        <div class="col-3">
                            <div style="height:30px">
                                <label for="image">File</label>
                            </div>
                            <div class="custom-file col-12">
                                <input type="file" class="custom-file-input" name="image" id="image">
                                <label class="custom-file-label" id="fileLabel" for="customFile">Choose file</label>
                                @error('image')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group col-3">
                            <label class="form-label required" for="name">Name</label>
                            <input type="text" class="form-control @error('name')  is-invalid @enderror" id="name"
                                name="name" value='{{ old('name') ? e(old('name')) : '' }}' placeholder="Enter name">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-3">
                            <label class="form-label required" for="quantity">Amount</label>
                            <input type="number" class="form-control @error('quantity')  is-invalid @enderror"
                                min="0" id="quantity" name="quantity" placeholder="Enter a integer value (e.g.,1,2)"
                                value='{{ old('quantity') ? e(old('quantity')) : 0 }}'>
                            @error('quantity')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-3">
                            <label class="form-label required" for="price">Price</label>
                            <input type="text" class="form-control @error('price')  is-invalid @enderror" id="price"
                                name="price" value='{{ old('price') ? e(old('price')) : '' }}'
                                placeholder="Enter a numeric value (e.g., 1.00)">
                            @error('price')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-3">
                            <label class="form-label required" for="discount_percent">Discount %</label>
                            <input type="number" class="form-control @error('discount_percent')  is-invalid @enderror"
                                id="discount_percent" name="discount_percent" min="0" value="0"
                                placeholder="Enter a integer value (e.g.,1,2)"
                                value='{{ old('discount_percent') ? e(old('discount_percent')) : '' }}'>
                            @error('discount_percent')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-3">
                            <label class="form-label required" for="name">Generate unique code</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('code')  is-invalid @enderror"
                                    id="code" name="code" value='{{ old('code') ? e(old('code')) : '' }}'
                                    placeholder="Generate code">
                                <span class="input-group-append">
                                    <button type="button" class="btn btn-primary btn-flat generateCode rounded-right">Generate</button>
                                </span>
                            </div>
                            @error('code')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-3">
                            <label class="form-label required">Expected delivery date:</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="far fa-calendar-alt"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control datepicker" name="expected_delivery_date">
                            </div>
                            @error('expected_delivery_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-3">
                            <label class="form-label required">Expected date of payment:</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="far fa-calendar-alt"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control datepicker" name="expected_date_of_payment">
                            </div>
                            @error('expected_date_of_payment')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-3">
                            <label class="form-label required">Suppliers</label>
                            <select data-live-search="true" class="form-control selectSupplier" name="supplier_id"
                                @error('supplier_id')  data-style="border-danger"  is-invalid @enderror>
                                <option value="0">Nothing selected</option>
                            </select>
                            @error('supplier_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-3">
                            <label class="form-label required">Categories (categories for a given supplier)</label>
                            <select data-live-search="true" class="form-control selectCategory" name="category_id"
                                @error('category_id')  data-style="border-danger"  is-invalid @enderror>
                                <option value="0">Nothing selected</option>
                            </select>
                            @error('category_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-3">
                            <label>Subcategories</label>
                            <select class="form-control selectSubCategory" name="subcategories[]" multiple
                                data-actions-box="true" data-dropup-auto="false">
                            </select>
                        </div>
                        <div class="form-group col-3">
                            <label>Brands (not necessarily)</label>
                            <select data-live-search="true" class="form-control selectBrands" multiple data-selected-text-format="count > 12"
                                data-actions-box="true" data-dropup-auto="false" multiple name="brands[]">
                            </select>
                        </div>
                        <div class="form-group col-3">
                            <label for="notes">Notes</label>
                            <textarea cols="3" rows="1" class="form-control" name="notes"></textarea>
                        </div>
                        <div class="form-group col-12">
                            <button type="submit" class="btn btn-primary">
                                Save changes
                            </button>
                        </div>
                    </div>
                    <div class="col-2 p-2">
                        <div class="row w-100 mt-4">
                            <div class="col-12">
                                <img name="cardWidgetImage" class="rounded w-100 m-0" src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png" />
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-footer bg-white pt-0">
            <table id="result" class="table table-hover">
                <thead class="bg-primary rounded-left rounded-right">
                    <tr>
                        <th class="rounded-left border-0 text-center">Initial amount</th>
                        <th class="border-0 text-center">Current amount</th>
                        <th class="border-0 text-center">Final price</th>
                        <th class="border-0 text-center">Regular price</th>
                        <th class="border-0 text-center">Unit discount price</th>
                        <th class="border-0 text-center">Unit price</th>
                        <th class="border-0 text-center rounded-right">Order amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border-left-0 border-top-0 border-right text-center" name="initial_amount"></td>
                        <td class="border-left-0 border-top-0 border-right text-center" name="current_amount"></td>
                        <td class="border-left-0 border-top-0 border-right text-center" name="final_price"></td>
                        <td class="border-left-0 border-top-0 border-right text-center" name="regular_price"></td>
                        <td class="border-left-0 border-top-0 border-right text-center" name="unit_discount_price"></td>
                        <td class="border-left-0 border-top-0 border-right text-center" name="unit_price"></td>
                        <td class="border-left-0 border-top-0 text-center" name="order_amount"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        <script type="text/javascript" src="{{ mix('js/purchases/form.js') }}"></script>
        <script type="text/javascript">
            const CATEGORY_ROUTE = "{{ route('api.categories') }}";
            const SUPPLIER_API_ROUTE = "{{ route('api.suppliers') }}";
            const BRAND_API_ROUTE = "{{ route('api.brands') }}";
        </script>
    @endpush
@endsection
