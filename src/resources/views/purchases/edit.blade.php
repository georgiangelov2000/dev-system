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
                        <form class="d-flex flex-wrap" action='{{ route('purchase.update', $product->id) }}' method='POST'
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row flex-wrap">
                                <div class="col-10 d-flex flex-wrap p-2">

                                    @if (count($product->images) < 3)
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
                                            id="name" name="name" value='{{ e($product->name) }}'
                                            placeholder="Enter name">
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="quantity">Quantity</label>
                                        <input type="number" placeholder="Enter quantity"
                                            class="form-control @error('quantity')  is-invalid @enderror" id="quantity"
                                            name="quantity" min="1" value='{{ e($product->quantity) }}'>
                                        @error('quantity')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="price">Price</label>
                                        <input type="text" class="form-control @error('price')  is-invalid @enderror"
                                            id="price" name="price" value='{{ e($product->price) }}'
                                            placeholder="Enter price">
                                        @error('price')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-6">
                                        <div class="form-group">
                                            <label for="name">Generate unique code</label>
                                            <div class="input-group">
                                                <input type="text"
                                                    class="form-control @error('code')  is-invalid @enderror" id="code"
                                                    name="code" value='{{ e($product->code) }}'
                                                    placeholder="Generate code">
                                                <span class="input-group-append">
                                                    <button type="button"
                                                        class="btn btn-info btn-flat generateCode">Generate</button>
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
                                                        {{ $supplier->id == $product->supplier_id ? 'selected' : '' }}>
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
                                                        {{ $category->id === $relatedProductData['productCategory'] ? 'selected' : '' }}
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
                                                        {{ in_array($subcategory['id'], $relatedProductData['productSubCategories']) ? 'selected' : '' }}
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
                                                        {{ in_array($brand->id, $relatedProductData['productBrands']) ? 'selected' : '' }}
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
                                </div>
                                <div class="col-2 p-2">
                                    <div class="row w-100">
                                        @if ($product->images && count($product->images) > 1)
                                            <div id="carouselExampleControls" class="col-12 carousel slide"
                                                data-ride="carousel">
                                                <div class="carousel-inner rounded">
                                                    @foreach ($product->images as $index => $image)
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
                                        @elseif ($product->images && count($product->images) === 1)
                                            <div class="col-12 mb-3">
                                                <img class="cardWidgetImage w-100 m-0"
                                                    src="{{ $product->images[0]->path .'/' . $product->images[0]->name }}" />
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
                        @if (count($product->images))
                            <div class="col-12 d-flex p-2">
                                @foreach ($product->images as $image)
                                    <div class="col-3 productImage" style="position: relative;">
                                        <img style="height: 200px; object-fit: contain;"
                                            class="d-block card card-widget widget-user w-100"
                                            src="{{ config('app.url') . $image->path  .'/' . $image->name }}">
                                        <form   
                                            id="deleteImageForm" 
                                            data-image-id="{{ $image->id }}"
                                            action="{{ route('purchase.delete.image', $product->id) }}" 
                                            method="POST"
                                            onsubmit="deleteImage(event)"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button 
                                                type="submit"
                                                id="deletePurchaseImage" 
                                                title="Delete" 
                                                class="btn btn-dark"
                                                style="position: absolute; opacity: 0.8; top: 50%; left: 50%; transform: translate(-50%, -50%);"
                                            >   
                                                <input type="hidden" value="{{$image->id}}" name="id">
                                                <i class="fa-light fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="col-6">
                                <span class="text-danger">The gallery is empty, please upload images for the current purchase</span>
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
