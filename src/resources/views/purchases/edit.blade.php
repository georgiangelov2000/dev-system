@extends('app')
@section('title', 'Edit purchase')

@section('content')
    <div class="card card-default cardTemplate">
        <div class="card-header">
            <div class="col-12">
                <h3 class="card-title">Edit purchase</h3>
            </div>
        </div>
        <div class="card-body">
            <form class="d-flex flex-wrap" action='{{ route('purchase.update', $product->id) }}' method='POST'
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row w-100 mb-2">
                    @if ($product->images)
                        <div class="col-3">
                            <img class="card card-widget widget-user w-100 h-100"
                                src="{{ $product->images ? $product->images->path . $product->images->name : 'https://leaveitwithme.com.au/wp-content/uploads/2013/11/dummy-image-square.jpg' }}" />
                        </div>
                    @endif
                    <div class="col-3 d-none imagePreview">
                        <div class="position-relative">
                            <img id="preview-image" alt="Preview" class="img-fluid card card-widget widget-user w-100 h-100 m-0">
                            <div class="ribbon-wrapper ribbon-lg">
                                <div class="ribbon bg-success text-lg">
                                    Preview
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group col-6">
                    <div style="height:30px">
                        <label for="image">File</label>
                    </div>
                    <div class="custom-file col-12">
                        <input type="file" name="image" id="image"
                            value="{{ $product->images ? $product->images->path . $product->images->name : '' }}"
                            class="custom-file-input">
                        <label class="custom-file-label" for="customFile">Choose file</label>
                    </div>
                </div>
                <div class="form-group col-6">
                    <label for="name">Name</label>
                    <input type="text" class="form-control @error('name')  is-invalid @enderror" id="name"
                        name="name" value='{{ e($product->name) }}' placeholder="Enter name">
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group col-6">
                    <label for="quantity">Quantity</label>
                    <input type="number" placeholder="Enter quantity"
                        class="form-control @error('quantity')  is-invalid @enderror" id="quantity" name="quantity"
                        value='{{ e($product->quantity) }}'>
                    @error('quantity')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group col-6">
                    <label for="price">Price</label>
                    <input type="text" class="form-control @error('price')  is-invalid @enderror" id="price"
                        name="price" value='{{ e($product->price) }}' placeholder="Enter price">
                    @error('price')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group col-6">
                    <div class="form-group">
                        <label for="name">Generate unique code</label>
                        <div class="input-group">
                            <input type="text" class="form-control @error('code')  is-invalid @enderror" id="code"
                                name="code" value='{{ e($product->code) }}' placeholder="Generate code">
                            <span class="input-group-append">
                                <button type="button" class="btn btn-info btn-flat generateCode">Generate</button>
                            </span>
                        </div>
                        @error('code')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea cols="3" rows="8" class="form-control" name="notes">{{ e($product->notes) }}</textarea>
                    </div>
                </div>
                <div class="form-group col-6">
                    <div class="form-group">
                        <label>Suppliers</label>
                        <select class="form-control selectSupplier" name="supplier_id">
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}"
                                    {{ $supplier->id == $relatedRecords['supplier'] ? 'selected' : '' }}>
                                    {{ $supplier->name }}</option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Categories (categories for a given supplier)</label>
                        <select class="form-control selectCategory" name="category_id">
                            <option value="0">Nothing selected</option>
                        </select>
                        @error('category_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Subcategories</label>
                        <select class="form-control selectSubCategory" name="subcategories[]" multiple>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Brands (not necessarily)</label>
                        <select class="form-control selectBrands" multiple data-selected-text-format="count > 12"
                            data-actions-box="true" data-dropup-auto="false" multiple name="brands[]">
                            @foreach ($brands as $brand)
                                <option {{ in_array($brand->id, $relatedRecords['brands']) ? 'selected' : '' }}
                                    value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        Save changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script type="text/javascript" src="{{ mix('js/purchases/form.js') }}"></script>

        <script type="text/javascript">
            let CATEGORY_ROUTE = "{{ route('api.categories') }}";
            let SELECTED_CATEGORY = "{{ $relatedRecords['category'] }}";
            let SELECTED_SUBCATEGORIES = "{{ $relatedRecords['sub_categories'] }}";
        </script>
    @endpush

@endsection
