@extends('app')

@section('content')

    <div class="row">
        <div class="card col-12 cardTemplate">

            <div class="card-header d-flex align-items-center p-2">
                <div class="col-10">
                    <h3 class="card-title">Brands</h3>
                </div>
                <div class="col-2 text-right">
                    <button type="button" class="btn btn-primary createBrand">
                        <i class="fa fa-plus"></i> Add brand
                    </button>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-3">
                        <div class="form-group actions d-none">
                            <label>Actions</label>
                            <select class="form-control selectAction">
                                <option value="0">Select Option</option>
                                <option value="delete">Delete</option>
                            </select>
                        </div>
                    </div>
                </div>
                <table id="brandsTable" class="table  table-hover table-sm dataTable no-footer">
                    <thead>
                        <tr>
                            <th>
                                <div class="form-check">
                                    <input class="form-check-input selectAll" type="checkbox">
                                    <label class="form-check-label" for="flexCheckDefault"></label>
                                </div>
                            </th>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Puchases</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>


    <!--create modal-->
    @include('templates.modal_form', [
        'labelOne' => 'name',
        'labelTwo' => 'description',
    
        'inputOne' => 'name',
        'inputTwo' => 'description',
    
        'title' => 'Add Brand',
    
        'formMethod' => 'post',
    
        'isFileAvailable' => true,
        'isAvailableMultiple' => false,
    ])

    <!--edit modal-->
    @include('templates.edit_modal_form', [
        'labelOne' => 'name',
        'labelTwo' => 'description',
    
        'inputOne' => 'name',
        'inputTwo' => 'description',
    
        'title' => 'Edit Brand',
    
        'formMethod' => 'post',

        'isFileAvailable' => true,
        'isAvailableMultiple' => false,
    ])

    @push('scripts')
        <script type="text/javascript" src="{{ mix('js/brands/brands.js') }}"></script>

        <script type="text/javascript">
            const BRAND_ROUTE = "{{ route('api.brands') }}";
            const REMOVE_BRAND_ROUTE = "{{ route('brand.delete', ':id') }}";
            const EDIT_BRAND_ROUTE = "{{ route('brand.edit', ':id') }}";
            const UPDATE_BRAND_ROUTE = "{{ route('brand.update', ':id') }}";
            const STORE_BRAND_ROUTE = "{{ route('brand.store') }}";
            const REMOVE_BRAND_IMAGE_ROUTE = "{{ route('brand.delete.image',':id') }}"
        </script>
    @endpush

@endsection
