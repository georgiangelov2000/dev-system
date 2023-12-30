@extends('app')

@section('content')
    <div class="row">
        <div class="card col-12 cardTemplate">
            <div class="card-header d-flex align-items-center p-2 bg-primary">
                <div class="col-10">
                    <h3 class="card-title">Role managment</h3>
                </div>
                <div class="col-2 text-right">
                    <a href="{{ route('roles.create') }}" type="button" class="btn btn-sm btn-light">
                        <i class="fa fa-plus"></i> Add Role
                    </a>
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
                <div class="row table-responsive">
                    <table id="rolesManagеmentTable" class="table table-hover table-sm dataTable no-footer">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Role</th>
                                <th>Permissions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        @push('scripts')
            <script type="text/javascript" src="{{ mix('js/roles_management/roles_management.js') }}"></script>
            <script type="text/javascript">
                const ROLES_MANAGEMENT_API_ROUTE = "{{ route('api.roles.management') }}"
                const ROLES_MANAGEMENT_EDIT_ROUTE = "{{ route('roles.edit',':id') }}";
            </script>
        @endpush
    @endsection
