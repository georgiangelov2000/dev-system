@extends('app')

@section('content')
    <div class="card card-default cardTemplate">

        <div class="card-header">
            <div class="col-12">
                <h3 class="card-title">Edit purchase: <span class="text-primary">{{ $status }}</span> </h3>
            </div>
        </div>

        <div class="card-body">
            <form class="d-flex flex-wrap" action='{{ route('purchase.update', $purchase->id) }}' method='POST'
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row flex-wrap">
                    <div class="col-10 d-flex flex-wrap p-2">
                        <div id="warning"
                            class="col-lg-12 col-xl-12 col-md-12 col-sm-12 card-footer rounded bg-white p-0 d-none">
                        </div>
                        <div class="form-group col-6">
                            <div style="height:30px">
                                <label for="image">File</label>
                            </div>
                            <div class="custom-file col-12">
                                <input type="file" name="image" id="image" class="custom-file-input">
                                <label class="custom-file-label" for="customFile">Choose file</label>
                            </div>
                        </div>
                        <div class="form-group col-6">
                            <label for="name">Name</label>
                            <input type="text" class="form-control @error('name')  is-invalid @enderror" id="name"
                                name="name" value='{{ e($purchase->name) }}' placeholder="Enter name">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-6">
                            <label for="quantity">Amount</label>
                            @if ($isEditable)
                                <input type="number" placeholder="Enter quantity"
                                    class="form-control @error('quantity')  is-invalid @enderror" id="quantity"
                                    name="quantity" min="{{ $purchase->initial_quantity > 0 ? 0 : 1 }}"
                                    value='{{ e($purchase->quantity) }}'>
                                @error('quantity')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            @else
                                <p class="input-group-text col-12">
                                    {{ $purchase->quantity }}
                                </p>
                            @endif
                        </div>
                        <div class="form-group col-6">
                            <label for="price">Price</label>
                            @if ($isEditable)
                                <input type="text" class="form-control @error('price')  is-invalid @enderror"
                                    id="price" name="price" value='{{ e($purchase->price) }}'
                                    placeholder="Enter price">
                                @error('price')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            @else
                                <p name="price" class="input-group-text col-12">€ {{ $purchase->price }}</p>
                            @endif
                        </div>
                        <div class="form-group col-6">
                            <div class="form-group">
                                <label for="initial_quantity">Initial amount</label>
                                <p name="initial_quantity" class="input-group-text col-12">
                                    {{ $purchase->initial_quantity }}</p>
                            </div>
                            <div class="form-group">
                                <label for="discount_percent">Discount %</label>
                                @if ($isEditable)
                                    <input type="number"
                                        class="form-control @error('discount_percent')  is-invalid @enderror"
                                        id="discount_percent" name="discount_percent" min="0"
                                        value='{{ $purchase->discount_percent ? $purchase->discount_percent : 0 }}'>
                                    @error('discount_percent')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                @else
                                    <p name="discount_percent" class="input-group-text col-12">
                                        {{ $purchase->discount_percent }}</p>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="name">Generate unique code</label>
                                @if ($isEditable)
                                    <div class="input-group">
                                        <input type="text" class="form-control @error('code')  is-invalid @enderror"
                                            id="code" name="code" value='{{ e($purchase->code) }}'
                                            placeholder="Generate code">
                                        <span class="input-group-append">
                                            <button type="button"
                                                class="btn btn-primary btn-flat generateCode">Generate</button>
                                        </span>
                                    </div>
                                    @error('code')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                @else
                                    <p class="input-group-text col-12">{{ $purchase->code }}</p>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>Delivery date:</label>
                                <div class="input-group">
                                    @if ($isEditable)
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-calendar-alt"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control datepicker" name="delivery_date"
                                            value="{{ $purchase->delivery_date ? date('m/d/Y', strtotime($purchase->delivery_date)) : '' }}" />
                                    @else
                                        <p class="input-group-text col-12">{{ $purchase->delivery_date }}</p>
                                    @endif
                                </div>
                                @error('delivery_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea cols="3" rows="5" class="form-control" name="notes">{{ e($purchase->notes) }}</textarea>
                            </div>
                        </div>
                        <div class="form-group col-6">
                            <div class="form-group">
                                <label id="order_amount">Orders amount</label>
                                <p name="order_amount" class="input-group-text col-12">{{ $orderAmount }}</p>
                            </div>
                            <div class="form-group">
                                <label>Suppliers</label>
                                <select class="form-control selectSupplier" name="supplier_id">
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}"
                                            {{ $supplier->id == $purchase->supplier_id ? 'selected' : '' }}>
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
                                    @foreach ($categories as $category)
                                        <option
                                            {{ $category->id === $relatedProductData['purchaseCategory'] ? 'selected' : '' }}
                                            value="{{ $category->id }}">{{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Subcategories</label>
                                <select class="form-control selectSubCategory" name="subcategories[]" multiple>
                                    @foreach ($relatedProductData['categorySubCategories'] as $subcategory)
                                        <option
                                            {{ in_array($subcategory['id'], $relatedProductData['purchaseSubCategories']) ? 'selected' : '' }}
                                            value="{{ $subcategory['id'] }}">{{ $subcategory['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Brands (not necessarily)</label>
                                <select class="form-control selectBrands" multiple data-selected-text-format="count > 12"
                                    data-actions-box="true" data-dropup-auto="false" multiple name="brands[]">
                                    <option value="0">Nothing selected</option>
                                    @foreach ($brands as $brand)
                                        <option
                                            {{ in_array($brand->id, $relatedProductData['purchaseBrands']) ? 'selected' : '' }}
                                            value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Expected date of payment:</label>
                                @if ($isEditable)
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-calendar-alt"></i>
                                            </span>
                                        </div>
                                        <input type="text"
                                            value="{{ date('m/d/Y', strtotime($purchase->expected_date_of_payment)) }}"
                                            class="form-control datepicker" name="expected_date_of_payment" />
                                    </div>
                                    @error('expected_date_of_payment')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                @else
                                    <p class="input-group-text col-12">
                                        {{ $purchase->expected_date_of_payment }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                Save changes
                            </button>
                        </div>
                    </div>
                    <div class="col-2 p-2">
                        <div class="row w-100">
                            @if ($purchase->image_path)
                                <div class="col-12">
                                    <img class="cardWidgetImage w-100 m-0 mb-2" src="{{ $purchase->image_path }}" />
                                </div>
                            @endif
                            <div class="col-12 d-none imagePreview">
                                <div class="position-relative">
                                    <img id="preview-image" alt="Preview"
                                        class="img-fluid cardWidgetImage card card-widget w-100  m-0">
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

    <div class="card card-default cardTemplate mt-2 mb-2">
        <div class="card-footer rounded bg-white p-0">
            <div class="row">
                <div class="col-lg-3 col-xl-3 col-md-3 col-sm-3">
                    <div class="description-block border-right">
                        <h5 id="final_price" class="description-header">0</h5>
                        <span class="description-text">Final price</span>
                    </div>
                </div>
                <div class="col-lg-3 col-xl-3 col-md-3 col-sm-3">
                    <div class="description-block border-right">
                        <h5 id="original_price" class="description-header">0</h5>
                        <span class="description-text">Regular price</span>
                    </div>
                </div>
                <div class="col-lg-2 col-xl-2 col-md-2 col-sm-2">
                    <div class="description-block border-right">
                        <h5 id="amount" class="description-header">0</h5>
                        <span class="description-text">Amount</span>
                    </div>
                </div>
                <div class="col-lg-2 col-xl-2 col-md-2 col-sm-2">
                    <div class="description-block border-right">
                        <h5 id="discount_price" class="description-header">0</h5>
                        <span class="description-text">Unit discount price</span>
                    </div>
                </div>
                <div class="col-lg-2 col-xl-2 col-md-2 col-sm-2">
                    <div class="description-block">
                        <h5 id="unit_price" class="description-header unitPrice">0</h5>
                        <span class="description-text">Unit price</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script type="text/javascript" src="{{ mix('js/purchases/form.js') }}"></script>

        <script type="text/javascript">
            const CATEGORY_ROUTE = "{{ route('api.categories') }}";
        </script>
    @endpush
@endsection
