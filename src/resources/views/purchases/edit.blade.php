@extends('app')

@section('content')
    <div class="card card-default cardTemplate">

        <div class="card-header">
            <div class="col-12">
                <h3 class="card-title">Edit purchase</h3>
            </div>
        </div>

        <div class="card cardTemplate card-tabs mb-0 border-0">
            <div class="card-header cardHeaderTemplate pb-0">
                <ul class="nav nav-pills" id="custom-tabs-one-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill"
                            href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home"
                            aria-selected="false">Form</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="custom-tabs-one-profile-tab" data-toggle="pill"
                            href="#custom-tabs-one-profile" role="tab" aria-controls="custom-tabs-one-profile"
                            aria-selected="true">Gallery</a>
                    </li>

                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-one-tabContent">
                    <div class="tab-pane active show" id="custom-tabs-one-home" role="tabpanel"
                        aria-labelledby="custom-tabs-one-home-tab">
                        @if (!$is_available)
                            <div class="alert alert-danger alert-dismissible col-10">
                                <h5><i class="icon fas fa-ban"></i> Alert!</h5>
                                The product has already been paid for, and as a result, cannot update some of the data. The
                                payment associated with the product indicates that the transaction has been completed, and
                                it's crucial to maintain the integrity of the payment records. To avoid any discrepancies or
                                potential conflicts, modifications to the product are restricted once a payment has been
                                made. If you need to make changes to the product details, it's advisable to create a new
                                entry or consider relevant adjustments within a new context.
                            </div>
                        @endif

                        <form class="d-flex flex-wrap" action='{{ route('purchase.update', $purchase->id) }}' method='POST'
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row flex-wrap">
                                <div class="col-10 d-flex flex-wrap p-2">

                                    @if (count($purchase->images) < 3)
                                        <div class="form-group col-6">
                                            <div style="height:30px">
                                                <label for="image">File</label>
                                            </div>
                                            <div class="custom-file col-12">
                                                <input type="file" name="image" id="image"
                                                    class="custom-file-input">
                                                <label class="custom-file-label" for="customFile">Choose file</label>
                                            </div>
                                        </div>
                                    @else
                                        <div class="form-group col-6 d-flex align-items-center mx-0 mb-0">
                                            <h6 class="text-danger mb-0">
                                                You reached maximum capacity of images for current purchase (3), you can
                                                delete
                                                the images from
                                                product gallery
                                            </h6>
                                        </div>
                                    @endif
                                    <div class="form-group col-6">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control @error('name')  is-invalid @enderror"
                                            id="name" name="name" value='{{ e($purchase->name) }}'
                                            placeholder="Enter name">
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="quantity">Quantity</label>
                                        @if ($is_available)
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
                                        @if ($is_available)
                                            <input type="text" class="form-control @error('price')  is-invalid @enderror"
                                                id="price" name="price" value='{{ e($purchase->price) }}'
                                                placeholder="Enter price">
                                            @error('price')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        @else
                                            <p class="input-group-text col-12">€ {{ $purchase->price }}</p>
                                        @endif
                                    </div>
                                    <div class="form-group col-6">
                                        <div class="form-group">
                                            <label for="discount_percent">Discount %</label>
                                            @if ($is_available)
                                                <input type="number"
                                                    class="form-control @error('discount_percent')  is-invalid @enderror"
                                                    id="discount_percent" name="discount_percent" min="0"
                                                    value='{{ $purchase->discount_percent ? $purchase->discount_percent : 0 }}'>
                                                @error('discount_percent')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            @else
                                                <p class="input-group-text col-12">{{ $purchase->discount_percent }}</p>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label for="name">Generate unique code</label>
                                            @if ($is_available)
                                                <div class="input-group">
                                                    <input type="text"
                                                        class="form-control @error('code')  is-invalid @enderror"
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
                                                @if ($is_available)
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="far fa-calendar-alt"></i>
                                                        </span>
                                                    </div>
                                                    <input type="text" class="form-control datepicker"
                                                        name="delivery_date"
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
                                            <select class="form-control selectSubCategory" name="subcategories[]"
                                                multiple>
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
                                            <select class="form-control selectBrands" multiple
                                                data-selected-text-format="count > 12" data-actions-box="true"
                                                data-dropup-auto="false" multiple name="brands[]">
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
                                            @if ($is_available)
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
                                        @if ($purchase->images && count($purchase->images) > 1)
                                            <div id="carouselExampleControls" class="col-12 carousel slide"
                                                data-ride="carousel">
                                                <div class="carousel-inner rounded">
                                                    @foreach ($purchase->images as $index => $image)
                                                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                            <img class="img-fluid cardWidgetImage d-block card card-widget w-100"
                                                                src="{{ $image->path . '/' . $image->name }}"
                                                                alt="Slide {{ $index + 1 }}">
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <a class="carousel-control-prev" href="#carouselExampleControls"
                                                    role="button" data-slide="prev">
                                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                    <span class="sr-only">Previous</span>
                                                </a>
                                                <a class="carousel-control-next" href="#carouselExampleControls"
                                                    role="button" data-slide="next">
                                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                    <span class="sr-only">Next</span>
                                                </a>
                                            </div>
                                        @elseif ($purchase->images && count($purchase->images) === 1)
                                            <div class="col-12 mb-3">
                                                <img class="cardWidgetImage w-100 m-0"
                                                    src="{{ $purchase->images[0]->path . '/' . $purchase->images[0]->name }}" />
                                            </div>
                                        @else
                                            <div class="col-12 mb-3">
                                                <img class="cardWidgetImage w-100 m-0"
                                                    src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/1665px-No-Image-Placeholder.svg.png" />
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
                    <div class="tab-pane fade" id="custom-tabs-one-profile" role="tabpanel"
                        aria-labelledby="custom-tabs-one-profile-tab">
                        @if (count($purchase->images))
                            <div class="col-12 d-flex p-2">
                                @foreach ($purchase->images as $image)
                                    <div class="col-3 productImage" style="position: relative;">
                                        <img style="height: 200px; object-fit: contain;"
                                            class="d-block card card-widget widget-user w-100"
                                            src="{{ config('app.url') . $image->path . '/' . $image->name }}">
                                        <form id="deleteImageForm" data-image-id="{{ $image->id }}"
                                            action="{{ route('purchase.delete.image', $purchase->id) }}" method="POST"
                                            onsubmit="deleteImage(event)">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" id="deletePurchaseImage" title="Delete"
                                                class="btn btn-dark"
                                                style="position: absolute; opacity: 0.8; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                                <input type="hidden" value="{{ $image->id }}" name="id">
                                                <i class="fa-light fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="col-6">
                                <span class="text-danger">The gallery is empty, please upload images for the current
                                    purchase</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

    </div>


    @push('scripts')
        <script type="text/javascript" src="{{ mix('js/purchases/form.js') }}"></script>

        <script type="text/javascript">
            let CATEGORY_ROUTE = "{{ route('api.categories') }}";
        </script>
    @endpush

@endsection
