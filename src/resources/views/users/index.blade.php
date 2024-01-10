@extends('app')

@section('content')

    <div class="row">
        <div class="card col-12 cardTemplate">

            <div class="card-header d-flex align-items-center p-2 bg-primary">
                <div class="col-10">
                    <h3 class="card-title">Staff members</h3>
                </div>
                <div class="col-2 text-right">
                    <a type="button" class="btn btn-sm btn-light" href="{{ route('user.create') }}" >
                        <i class="fa fa-plus"></i> Create member
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-3">
                        <div class="form-group">
                            <label for="role_id">Roles</label>
                            <select class="form-control" name="role_id" id="role_id">
                                <option value="">All</option>
                                @foreach (config('statuses.roles') as $key => $item)
                                    <option value="{{$key}}">{{$item}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <table id="users" class="table table-hover table-sm">
                    <thead>
                        <tr>
                            <th>ON/OFF</th>
                            <th>ID</th>
                            <th>Last seen</th>
                            <th>Image</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Username</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Contract</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

@push('scripts')
    <script type="text/javascript" src="{{mix('js/users/users.js')}}"></script>
    <script type="text/javascript">
        let API_USER_ROUTE = "{{route('api.users')}}";
        let EDIT_USER_ROUTE = "{{ route('user.edit', ':id') }}";
    </script>
@endpush

@endsection
