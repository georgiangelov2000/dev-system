@extends('app')

@section('content')
    <div class="row">
        <div class="card col-12 cardTemplate">
            <div class="card-header bg-primary d-flex align-items-center p-2">
                <div class="col-10">
                    <h3 class="card-title">Subcategories</h3>
                </div>
                <div class="col-2 text-right">
                    <button type="button" class="btn btn-sm btn-light createSubCategory">
                        <i class="fa fa-plus"></i> Add subcategory
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
                <table id="subCategoriesTable" class="table table-hover table-sm dataTable no-footer">
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
                            <th>Category</th>
                            <th>Purchases</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <!--create modal-->
        @include('templates.single_modal_form', [
            'title' => 'Create subcategory',
            'labelOne' => 'name',
            'inputOne' => 'name',
            'formMethod' => 'POST',
        ])

        @include('templates.edit_single_modal_form', [
            'title' => 'Edit subcategory',
            'labelOne' => 'name',
            'inputOne' => 'name',
            'formMethod' => 'POST',
            'inputType' => 'text',
            'isDatePicker' => false
        ])

        @push('scripts')
            <script type="text/javascript" src="{{ mix('js/subcategories/subcategories.js') }}"></script>
            <script type="text/javascript">
                const SUB_CATEGORY_API_ROUTE = "{{ route('api.subcategories') }}"
                const STORE_SUB_CATEGORY_ROUTE = "{{route('subcategory.store')}}";
                const DELETE_SUB_CATEGORY_ROUTE = "{{route('subcategory.delete',':id')}}";
                const EDIT_SUB_CATEGORY_ROUTE = "{{route('subcategory.edit',':id')}}";
                const UPDATE_SUB_CATEGORY_ROUTE = "{{route('subcategory.update',':id')}}";
            </script>
        @endpush

    @endsection
