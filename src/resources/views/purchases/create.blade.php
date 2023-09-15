@extends('app')

@section('content')
    <div class="card card-default cardTemplate">
        <div class="card-header">
            <div class="col-12">
                <h3 class="card-title">Create purchase</h3>
            </div>
        </div>
        <div class="card-body">
            <form class="d-flex flex-wrap" action='{{ route('purchase.store') }}' method='POST' enctype="multipart/form-data">
                @csrf

                <div class="row flex-wrap">
                    <div class="col-10 d-flex flex-wrap p-2">
                        <div class="col-6">
                            <div style="height:30px">
                                <label for="image">File</label>
                            </div>
                            <div class="custom-file col-12">
                                <input type="file" class="custom-file-input" name="image" id="image">
                                <label class="custom-file-label" for="customFile">Choose file</label>
                                @error('image')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group col-6">
                            <label for="name">Name</label>
                            <input type="text" class="form-control @error('name')  is-invalid @enderror" id="name"
                                name="name" value='{{ old('name') ? e(old('name')) : '' }}' placeholder="Enter name">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-6">
                            <label for="quantity">Quantity</label>
                            <input type="number"
                                class="form-control @error('quantity')  is-invalid @enderror" min="0" id="quantity" name="quantity" placeholder="Enter a integer value (e.g.,1,2)"
                                value='{{ old('quantity') ? e(old('quantity')) : '' }}'>
                            @error('quantity')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-6">
                            <label for="price">Price</label>
                            <input type="text" class="form-control @error('price')  is-invalid @enderror" id="price" 
                                name="price" value='{{ old('price') ? e(old('price')) : '' }}' placeholder="Enter a numeric value (e.g., 1.00)">
                            @error('price')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-6 mb-0">
                            <div class="form-group">
                                <label for="discount_percent">Discount %</label>
                                <input 
                                    type="number" 
                                    class="form-control @error('discount_percent')  is-invalid @enderror" 
                                    id="discount_percent"
                                    name="discount_percent" 
                                    min="0"
                                    placeholder="Enter a integer value (e.g.,1,2)"
                                    value='{{ old('discount_percent') ? e(old('discount_percent')) : '' }}' 
                                    >
                                @error('discount_percent')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="name">Generate unique code</label>
                                <div class="input-group">
                                    <input type="text" class="form-control @error('code')  is-invalid @enderror"
                                        id="code" name="code" value='{{ old('code') ? e(old('code')) : '' }}'
                                        placeholder="Generate code">
                                    <span class="input-group-append">
                                        <button type="button" class="btn btn-primary btn-flat generateCode">Generate</button>
                                    </span>
                                </div>
                                @error('code')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Delivery date:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control datepicker" name="delivery_date">
                                </div>
                                @error('delivery_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea cols="3" rows="5" class="form-control" name="notes"></textarea>
                            </div>
                        </div>
                        <div class="form-group col-6">
                            <div class="form-group">
                                <label>Suppliers</label>
                                <select class="form-control selectSupplier" name="supplier_id"
                                    @error('supplier_id')  data-style="border-danger"  is-invalid @enderror>
                                    <option value="0">Nothing selected</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Categories (categories for a given supplier)</label>
                                <select class="form-control selectCategory" name="category_id"
                                    @error('category_id')  data-style="border-danger"  is-invalid @enderror>
                                    <option value="0">Nothing selected</option>
                                </select>
                                @error('category_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Subcategories</label>
                                <select class="form-control selectSubCategory" name="subcategories[]" multiple
                                    data-actions-box="true" data-dropup-auto="false">
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Brands (not necessarily)</label>
                                <select class="form-control selectBrands" multiple data-selected-text-format="count > 12"
                                    data-actions-box="true" data-dropup-auto="false" multiple name="brands[]">
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Expected date of payment:</label>
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
                        </div>
                        <div class="form-group col-12">
                            <button type="submit" class="btn btn-primary">
                                Save changes
                            </button>
                        </div>
                    </div>
                    <div class="col-2 p-2">
                        <div class="row w-100 mb-2">
                            <div class="col-12 d-none imagePreview">
                                <div class="position-relative">
                                    <img id="preview-image" alt="Preview"
                                        class="img-fluid w-100 h-100 m-0">
                                    <div class="ribbon-wrapper ribbon-lg">
                                        <div class="ribbon bg-success text-lg">
                                            Preview
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script type="text/javascript" src="{{ mix('js/purchases/form.js') }}"></script>
        <script type="text/javascript">
            let CATEGORY_ROUTE = "{{ route('api.categories') }}";
        </script>
    @endpush

@endsection
