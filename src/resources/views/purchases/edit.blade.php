@extends('app')

@section('content')
    <div class="card card-default cardTemplate">

        <div class="card-header bg-primary">
            <div class="col-12">
                <h3 class="card-title">Edit purchase</h3>
            </div>
        </div>

        <div class="card-body">

            @if (!$purchase->is_editable)
                <div class="d-flex flex-wrap">
                    <div class="alert alert-danger" role="alert">
                        The purchase is currently marked as <a href="#" class="alert-link"> {{ $purchase->status }}</a>.
                        Please note that certain fields cannot be edited as the purchase is already closed. <a
                            href="#" class="alert-link">Delivery date: {{ $purchase->delivery_date }}</a>.
                    </div>
                    <div class="row flex-wrap"></div>
                </div>
            @endif

            @if ($purchase->is_editable)
                <form class="d-flex flex-wrap" action='{{ route('purchases.update', $purchase->id) }}' method='POST'
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
            @else
                <div class="d-flex flex-wrap">
            @endif

            <div class="row flex-wrap">
                <div class="col-10 d-flex flex-wrap p-2">

                    @if ($purchase->is_editable)
                        <div class="col-12 d-flex flex-wrap p-0">
                            <h5 class="col-12 pt-2"> <i class="fa-light fa-file"></i> File uploading</h5>
                            <div class="col-12">
                                <hr class="mt-0">
                            </div>
                            <div class="form-group col-12 pb-0">
                                <div style="height:30px">
                                    <label for="image">File</label>
                                </div>
                                <div class="custom-file col-12">
                                    <input type="file" name="image" id="image" class="custom-file-input">
                                    <label class="custom-file-label" for="customFile">Choose file</label>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="col-12 d-flex flex-wrap p-0">
                        <h5 class="col-12 pt-2"> <i class="fa-light fa-circle-info"></i> Main Information</h5>
                        <div class="col-12">
                            <hr class="mt-0">
                        </div>
                        <div class="form-group col-3">
                            <label class="form-label required" for="name">Name</label>
                            @if (!$purchase->is_editable)
                                <p class="input-group-text col-12 border-0">{{ $purchase->name }}</p>
                            @else
                                <input type="text" class="form-control @error('name')  is-invalid @enderror"
                                    id="name" name="name" value='{{ e($purchase->name) }}'
                                    placeholder="Enter name" />
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            @endif
                        </div>
                        <div class="form-group col-3">
                            <label class="form-label required">Suppliers</label>
                            @if (!$purchase->is_editable)
                                <p name="supplier-label" class="input-group-text col-12 border-0">
                                    {{ $purchase->supplier->name ?? '' }}
                                </p>
                            @else
                                <select class="form-control selectSupplier" name="supplier_id" data-live-search="true">
                                    <option selected value="{{ $purchase->supplier->id }}">
                                        {{ $purchase->supplier->name }}
                                    </option>
                                </select>
                                @error('supplier_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            @endif
                        </div>
                        <div class="form-group col-3">
                            <label class="form-label required">Categories (categories for a given supplier)</label>
                            @if (!$purchase->is_editable)
                                <p name="category-label" class="input-group-text col-12 border-0">
                                    {{ $purchase->category->name }}
                                </p>
                            @else
                                <select class="form-control selectCategory" name="category_id">
                                    <option selected value="{{ $purchase->category->id }}">
                                        {{ $purchase->category->name }}
                                    </option>
                                </select>
                                @error('category_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            @endif
                        </div>
                        <div class="form-group col-3">
                            <label>Sub categories</label>
                            @if (!$purchase->is_editable)
                                <p name="subcategories-label" class="input-group-text col-12 border-0">
                                    @if (count($purchase->sub_categories))
                                        @php
                                            $subCategoryNames = $purchase->sub_categories->pluck('name')->implode(', ');
                                        @endphp
                                        <span class="badge bg-dark text-light">
                                            {{ $subCategoryNames }}
                                        </span>
                                    @else
                                        Nothing selected
                                    @endif
                                </p>
                            @else
                                <select multiple data-selected-text-format="count > 2"
                                    class="form-control selectSubCategory" name="subcategories[]" multiple>

                                </select>
                            @endif
                        </div>
                        <div class="form-group col-3">
                            <label>Brands (not necessarily)</label>
                            @if (!$purchase->is_editable)
                                <p name="subcategories-label" class="input-group-text col-12 border-0 pt-2 pb-2">
                                    @if (count($purchase->brands))
                                        @php
                                            $brandNames = $purchase->brands->pluck('name')->implode(', ');
                                        @endphp
                                        <span class="badge bg-dark text-light">
                                            {{ $brandNames }}
                                        </span>
                                    @else
                                        Nothing selected
                                    @endif
                                </p>
                            @else
                                <select class="form-control selectBrands" multiple data-selected-text-format="count > 12"
                                    data-actions-box="true" data-dropup-auto="false" multiple name="brands[]"
                                    data-live-search="true">
                                    <option value="0">Nothing selected</option>
                                    @if (count($purchase->brands))
                                        @foreach ($purchase->brands as $brand)
                                            <option selected value="{{ $brand->id }}">{{ $brand->name }} </option>
                                        @endforeach
                                    @endif
                                </select>
                            @endif
                        </div>
                        <div class="form-group col-3">
                            <label class="form-label required" for="name">Generate unique code</label>
                            @if ($purchase->is_editable)
                                <div class="input-group">
                                    <input type="text" class="form-control @error('code')  is-invalid @enderror"
                                        id="code" name="code" value='{{ e($purchase->code) }}'
                                        placeholder="Generate code">
                                    <span class="input-group-append">
                                        <button type="button"
                                            class="btn btn-primary btn-flat generateCode rounded-right">Generate</button>
                                    </span>
                                </div>
                                @error('code')
                                    <span name="code" class="text-danger">{{ $message }}</span>
                                @enderror
                            @else
                                <p class="input-group-text col-12 border-0">{{ $purchase->code }}</p>
                            @endif
                        </div>
                        <div class="form-group col-3">
                            <label for="notes">Notes</label>
                            @if (!$purchase->is_editable)
                                <p name="notes-label"
                                    class="{{ strlen($purchase->notes) ? 'input-group-text col-12 border-0' : 'input-group-text col-12 border-0' }}"
                                    class="input-group-text col-12 border-0">
                                    @if (strlen($purchase->notes) > 0)
                                        {{ $purchase->notes }}
                                    @else
                                        <br>
                                    @endif
                                </p>
                            @else
                                <textarea cols="3" rows="1" class="form-control" name="notes">{{ e($purchase->notes) }}</textarea>
                            @endif
                        </div>
                        <div class="form-group col-3">
                            <label for="color">Color</label>
                            @if (!$purchase->is_editable)
                                <p class="input-group-text col-12 border-0">
                                    <br>
                                    @if(!$purchase->color)
                                        <span class="badge bg-dark text-light">
                                            NONE 
                                        </span>
                                    @else
                                        <span class="badge w-25 text-light" style="background-color:{{ $purchase->color }}">{{ $purchase->color }}</span>
                                    @endif
                                </p>
                            @else
                                <input 
                                    type="color" 
                                    id="color" 
                                    name="color" 
                                    value="{{ $purchase->color }}" 
                                    class="form-control"
                                >
                            @endif
                        </div>
                    </div>

                    <div class="col-12 d-flex flex-wrap p-0">
                        <h5 class="col-12 pt-2"> <i class="fa-light fa-calculator"></i> Calculation fields</h5>
                        <div class="col-12">
                            <hr class="mt-0">
                        </div>
                        <div class="form-group col-2">
                            <label class="form-label required" for="quantity">Remaining amount</label>
                            @if ($purchase->is_editable)
                                <input type="number" placeholder="Enter quantity"
                                    class="form-control @error('quantity')  is-invalid @enderror" id="quantity"
                                    name="quantity" min="{{ $purchase->initial_quantity > 0 ? 0 : 1 }}"
                                    value='{{ e($purchase->quantity) }}'>
                                @error('quantity')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            @else
                                <input disabled name="quantity" class="form-control border-0"
                                    value="{{ $purchase->quantity }}" />
                            @endif
                        </div>
                        <div class="form-group col-2">
                            <label class="form-label required" for="price">Price</label>
                            @if ($purchase->is_editable)
                                <input type="text" class="form-control @error('price')  is-invalid @enderror"
                                    id="price" name="price" value='{{ e($purchase->price) }}'
                                    placeholder="Enter price">
                                @error('price')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            @else
                                <input disabled name="price" class="form-control border-0"
                                    value="{{ $purchase->price }}" />
                            @endif
                        </div>
                        <div class="form-group col-2">
                            <label class="form-label required" for="discount_percent">Discount %</label>
                            @if ($purchase->is_editable)
                                <input type="number"
                                    class="form-control @error('discount_percent')  is-invalid @enderror"
                                    id="discount_percent" name="discount_percent" min="0"
                                    value='{{ $purchase->discount_percent ? $purchase->discount_percent : 0 }}'>
                                @error('discount_percent')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            @else
                                <input disabled name="discount_percent" class="form-control border-0"
                                    value="{{ $purchase->discount_percent }}" />
                            @endif
                        </div>
                        <div class="form-group col-2">
                            <label class="form-label required" for="discount_percent">Weight</label>
                            @if (!$purchase->is_editable)
                                <p name="weight-label" class="input-group-text col-12 border-0">
                                    {{ $purchase->weight }}
                                </p>
                            @else
                                <input type="number" class="form-control @error('weight')  is-invalid @enderror"
                                    id="weight" name="weight" min="0"
                                    placeholder="Enter a integer value (e.g.,1,2)"
                                    value="{{ $purchase->weight ?? e(old('weight')) }}"
                                />
                                @error('weight')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            @endif

                        </div>
                        <div class="form-group col-2">
                            <label class="form-label required" for="height">Height</label>
                            @if (!$purchase->is_editable)
                                <p name="height-label" class="input-group-text col-12 border-0">
                                    {{ $purchase->height }}
                                </p>
                            @else
                                <input type="number" class="form-control @error('height')  is-invalid @enderror"
                                    id="height" name="height" min="0" value="{{ $purchase->height ?? e(old('height')) }}"
                                    placeholder="Enter a integer value (e.g.,1,2)"
                                />
                                @error('height')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            @endif
                        </div>
                    </div>

                    <div class="col-12 d-flex flex-wrap p-0">
                        <h5 class="col-12 pt-2"> <i class="fa-light fa-calendar-days"></i> Expected dates</h5>
                        <div class="col-12">
                            <hr class="mt-0">
                        </div>
                        <div class="form-group col-3">
                            <label class="form-label required">Expected date of payment:</label>
                            @if ($purchase->is_editable)
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <input type="text"
                                        value="{{ date('m/d/Y', strtotime($purchase->payment->expected_date_of_payment)) }}"
                                        class="form-control datepicker" name="expected_date_of_payment" />
                                </div>
                                @error('expected_date_of_payment')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            @else
                                <input disabled name="expected_date_of_payment" class="form-control border-0"
                                    value="{{ $purchase->expected_date_of_payment }}" />
                            @endif
                        </div>
                        <div class="form-group col-3">
                            <label class="form-label required">Expected delivery date:</label>
                            <div class="input-group">
                                @if ($purchase->is_editable)
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control datepicker" name="expected_delivery_date"
                                        value="{{ date('m/d/Y', strtotime($purchase->expected_delivery_date)) }}" />
                                @else
                                    <input disabled name="expected_delivery_date" class="form-control border-0"
                                        value="{{ $purchase->expected_delivery_date }}" />
                                @endif
                            </div>
                            @error('delivery_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    @if ($purchase->is_editable)
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                Save changes
                            </button>
                        </div>
                    @endif

                </div>
                <div class="col-2 p-2">
                    <h5 class="text-center mt-4"></h5>
                    <div class="row w-100">
                        @if ($purchase->image_path)
                            <div class="col-12">
                                <img name="cardWidgetImage" class="rounded w-100 m-0"
                                    src="{{ $purchase->image_path }}" />
                            </div>
                        @else
                            <div class="col-12">
                                <img name="cardWidgetImage" class="w-100 m-0"
                                    src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png" />
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if ($purchase->is_editable)
                </form>
            @else
                    </div>
            @endif
    </div>
    <div class="card-footer bg-white pt-0">
        <table id="result" class="table table-hover">
            <thead class="bg-primary rounded-left rounded-right">
                <tr>
                    <th class="rounded-left border-0 text-center">Initial amount</th>
                    <th class="border-0 text-center">Remaining amount</th>
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
            // ROUTES
            const CATEGORY_ROUTE = "{{ route('api.categories') }}";
            const SUB_CATEGORY_ROUTE = "{{ route('api.subcategories') }}";
            const SUPPLIER_API_ROUTE = "{{ route('api.suppliers') }}";
            const BRAND_API_ROUTE = "{{ route('api.brands') }}";

            // ON EDIT
            const IS_EDIT = "{{ $purchase->id }}";
            const IS_EDITABLE = "{{ $purchase->is_editable }}";
            const CATEGORY_ID = "{{ $purchase->category->id }}"
            const SUB_CATEGORIES = @json($purchase->sub_categories->pluck('id')->toArray());

            const initialQuantity = "{{ $purchase->initial_quantity }}";
            const purchaseOrderAmount = "{{ $purchase->order_amount }}";
        </script>
    @endpush
@endsection
