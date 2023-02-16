@extends('app')
@section('title', 'Brands')

@section('content')
<div id='categories-page'>
    <div class="container">
        <div class="row justify-content-between mb-3">
            <h3 class="mb-0">Brands</h3>
            <button type="button" class="btn btn-primary createBrand">
                <i class="fa fa-plus"></i> Add brand
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
                                <th>Name</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!--create modal-->
    @include('templates.modal_form',[
    'labelOne' => "name",
    'labelTwo' => "description",

    'inputOne' => "name",
    'inputTwo' => "description",

    'title'=>"Add Brand",

    'formMethod' => "post",
    ])

    <!--create modal-->
    @include('templates.edit_modal_form',[
    'labelOne' => "name",
    'labelTwo' => "description",

    'inputOne' => "name",
    'inputTwo' => "description",

    'title'=>"Edit Brand",

    'formMethod' => "post",
    ])

    @push('scripts')
    <script type="text/javascript" src="{{ mix('js/brands.js') }}"></script>

    <script type="text/javascript">
        let BRAND_ROUTE = "{{route('api.brands')}}";
        let REMOVE_BRAND_ROUTE = "{{route('brand.delete',':id')}}";
        let EDIT_BRAND_ROUTE = "{{route('brand.edit',':id')}}";
        let UPDATE_BRAND_ROUTE = "{{route('brand.update',':id')}}";
        let STORE_BRAND_ROUTE = "{{route('brand.store')}}";
    </script>
    @endpush

    @endsection
