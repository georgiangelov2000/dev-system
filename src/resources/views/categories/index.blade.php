@extends('app')

@section('content')
        <div class="row">
            <div class="card col-12 cardTemplate">
                <div class="card-header d-flex align-items-center p-2">
                    <div class="col-10">
                        <h3 class="card-title">Categories</h3>
                    </div>
                    <div class="col-2 text-right">
                        <button type="button" class="btn btn-primary createCategory">
                            <i class="fa fa-plus"></i> Add category
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
                    <table id="categoriesTable" class="table  table-hover table-sm dataTable no-footer">
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
                                <th>Description</th>
                                <th>Subcategories</th>
                                <th>Purchases</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

    <!--create modal-->
    @include('templates.modal_form',[
        'labelOne' => "name",
        'labelTwo' => "description",

        'inputOne' => "name",
        'inputTwo' => "description",

        'title'=>"Add Category",

        'formMethod' => "post",
        
        'isFileAvailable' => true,
        'isAvailableMultiple' => true
    ])

    <!--edit modal-->
    @include('templates.edit_modal_form',[
        'labelOne' => "name",
        'labelTwo' => "description",

        'inputOne' => "name",
        'inputTwo' => "description",

        'title'=>"Edit Category",

        'formMethod' => "post",    
        
        'isFileAvailable' => true,
        'isAvailableMultiple' => true
    ])

    @push('scripts')
    <script type="text/javascript" src="{{ mix('js/categories/categories.js') }}"></script>
    <script type="text/javascript">
        let CATEGORY_ROUTE = "{{route('api.categories')}}";
        let PRODUCT_API_ROUTE = "{{ route('api.products') }}";
        let REMOVE_CATEGORY_ROUTE = "{{route('category.delete',':id')}}";
        let EDIT_CATEGORY_ROUTE = "{{route('category.edit',':id')}}";
        let UPDATE_CATEGORY_ROUTE = "{{route('category.update',':id')}}";
        let STORE_CATEGORY_ROUTE = "{{route('category.store')}}";
        let SUBCATEGORY_ROUTE = "{{route('category.detach.subcategory',':id')}}";
        const REMOVE_CATEGORY_IMAGE_ROUTE = "{{ route('category.delete.image',':id') }}"
    </script>
    @endpush

    @endsection
