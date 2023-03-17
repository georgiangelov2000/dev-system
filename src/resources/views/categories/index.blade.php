@extends('app')
@section('title', 'Categories')

@section('content')
        <div class="row justify-content-between mb-3">
            <h3 class="mb-0">Categories</h3>
            <button type="button" class="btn btn-primary createCategory">
                <i class="fa fa-plus"></i> Add category
            </button>   
        </div>
        <div class="row">
            <div class="card col-12 cardTemplate">
                <div class="card-body">
                    <div class="row">
                        <div class="col-3">
                            <div class="form-group actions d-none">
                                <label>Actions</label>
                                <select class="form-control form-control-sm selectAction">
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
                                <th>Name</th>
                                <th>Description</th>
                                <th>Subcategories</th>
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
    
    'isAvailableMultiple' => true
    ])

    <!--create modal-->
    @include('templates.edit_modal_form',[
    'labelOne' => "name",
    'labelTwo' => "description",

    'inputOne' => "name",
    'inputTwo' => "description",

    'title'=>"Edit Category",

    'formMethod' => "post",    
    
    'isAvailableMultiple' => true
    ])

    @push('scripts')
    <script type="text/javascript" src="{{ mix('js/categories.js') }}"></script>
    <script type="text/javascript">
        let CATEGORY_ROUTE = "{{route('api.categories')}}";
        let REMOVE_CATEGORY_ROUTE = "{{route('category.delete',':id')}}";
        let EDIT_CATEGORY_ROUTE = "{{route('category.edit',':id')}}";
        let UPDATE_CATEGORY_ROUTE = "{{route('category.update',':id')}}";
        let STORE_CATEGORY_ROUTE = "{{route('category.store')}}";
        let SUBCATEGORY_ROUTE = "{{route('category.detach.subcategory',':id')}}";
    </script>
    @endpush

    @endsection
