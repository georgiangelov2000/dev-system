@extends('app')

@section('content')
    <div class="card card-default cardTemplate">

        <div class="card-header bg-primary">
            <div class="col-12">
                <h3 class="card-title">Edit purchase</h3>
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
                        <div class="form-group col-3">
                            <div style="height:30px">
                                <label for="image">File</label>
                            </div>
                            <div class="custom-file col-12">
                                <input type="file" name="image" id="image" class="custom-file-input">
                                <label class="custom-file-label" for="customFile">Choose file</label>
                            </div>
                        </div>
                        <div class="form-group col-3">
                            <label class="form-label required" for="name">Name</label>
                            <input type="text" class="form-control @error('name')  is-invalid @enderror" id="name"
                                name="name" value='{{ e($purchase->name) }}' placeholder="Enter name">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-3">
                            <label class="form-label required" for="quantity">Amount</label>
                            @if ($purchase->is_editable)
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
                        <div class="form-group col-3">
                            <label class="form-label required" for="price">Price</label>
                            @if ($purchase->is_editable)
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
                        <div class="form-group col-3">
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
                                <p name="discount_percent" class="input-group-text col-12">
                                    {{ $purchase->discount_percent }}</p>
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
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            @else
                                <p class="input-group-text col-12">{{ $purchase->code }}</p>
                            @endif
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
                                        value="{{ date('m/d/Y', strtotime($purchase->payment->expected_date_of_payment))}}"
                                        class="form-control datepicker" name="expected_date_of_payment" />
                                </div>
                                @error('expected_date_of_payment')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            @else
                                <p class="input-group-text col-12">{{ $purchase->payment->expected_date_of_payment }}</p>
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
                                    <p class="input-group-text col-12">{{ $purchase->expected_delivery_date }}</p>
                                @endif
                            </div>
                            @error('delivery_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-3">
                            <label class="form-label required">Suppliers</label>
                            <select class="form-control selectSupplier" name="supplier_id" data-live-search="true">
                                <option selected value="{{ $purchase->supplier->id }}">
                                    {{ $purchase->supplier->name }}
                                </option>
                            </select>
                            @error('supplier_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-3">
                            <label class="form-label required">Categories (categories for a given supplier)</label>
                            <select class="form-control selectCategory" name="category_id">
                                <option value="0">Nothing selected</option>
                                @if(count($purchase->categories))
                                    @foreach ($purchase->categories as $category)
                                        <option selected value="{{ $category->id }}">{{ $category->name }} </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('category_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-3">
                            <label>Subcategories</label>
                            <select multiple data-selected-text-format="count > 2" class="form-control selectSubCategory" name="subcategories[]" multiple>
                               @if(count($purchase->subcategories))
                                    @foreach ($purchase->subcategories as $subcategory)
                                        <option value="{{ $subcategory->id }}">{{ $subcategory->name }} </option>
                                    @endforeach
                               @endif
                            </select>
                        </div>
                        <div class="form-group col-3">
                            <label>Brands (not necessarily)</label>
                            <select class="form-control selectBrands" multiple data-selected-text-format="count > 12"
                                data-actions-box="true" data-dropup-auto="false" multiple name="brands[]" data-live-search="true">
                                <option value="0">Nothing selected</option>
                                @if(count($purchase->brands))
                                    @foreach ($purchase->brands as $brand)
                                        <option selected value="{{ $brand->id }}">{{ $brand->name }} </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="form-group col-3">
                            <label for="notes">Notes</label>
                            <textarea cols="3" rows="1" class="form-control" name="notes">{{ e($purchase->notes) }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                Save changes
                            </button>
                        </div>
                    </div>
                    <div class="col-2 p-2">
                        <h5 class="text-center mt-4"></h5>
                        <div class="row w-100">
                            @if ($purchase->image_path)
                                <div class="col-12">
                                    <img name="cardWidgetImage" class="rounded w-100 m-0" src="{{ $purchase->image_path }}" />
                                </div>
                            @else
                                <div class="col-12">
                                    <img name="cardWidgetImage" class="w-100 m-0" src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png" />
                                </div>
                            @endif
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
            const SUB_CATEGORY_ROUTE = "{{ route('api.subcategories') }}";
            const SUPPLIER_API_ROUTE = "{{ route('api.suppliers') }}";
            const BRAND_API_ROUTE = "{{ route('api.brands') }}";

            const initialQuantity = "{{ $purchase->initial_quantity }}";
            const purchaseOrderAmount = "{{ $purchase->order_amount }}";
        </script>
    @endpush
@endsection
