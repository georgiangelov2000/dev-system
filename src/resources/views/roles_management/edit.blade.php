@extends('app')

@section('content')
<div class="card card-default cardTemplate">
    <div class="card-header bg-primary">
        <div class="col-12">
            <h3 class="card-title">Edit Role Management</h3>
        </div>
    </div>
    <div class="card-body">
        <div class="col-12">
            <form action='{{ route('roles.update',$role->id) }}' method='POST'>
                @csrf
                @method('PUT')
                
                <div class="row flex-wrap">

                    <div class="col-12 d-flex">
                        <div class="form-group col-3">
                            <label class="form-label required" for="name">Role Name</label>
                            <input type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                id="name" name="name"
                                value='{{ e($role->name) }}'
                                placeholder="Enter name"
                            >
                            @error('name')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-3">
                            <label class="form-label" for="users">Assign users</label>
                            <select multiple class="form-control" name="users[]" id="users"  multiple data-selected-text-format="count > 2">
                                @forelse ($users as $user)
                                <option value="{{ $user->id }}" {{ in_array($user->id, $role->userRoles->pluck('id')->toArray()) ? 'selected' : '' }}>
                                    {{ $user->username }}
                                </option>
                                @empty
                                <option value="" disabled>No users available</option>
                                @endforelse
                            </select>
                            @error('users')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>                        
                    </div>

                    <div class="col-12">
                        <div class="form-group col-6 d-flex flex-wrap align-items-center">
                            <label class="form-label required col-12 p-0" for="permissions">Permissions</label>
                            @forelse ($access_management as $item)
                            <div class="form-check mr-2 pr-2 d-flex align-items-center border border-right-1 border-left-0 border-top-0 border-bottom-0">
                                <input class="form-check-input mt-0"
                                    type="checkbox"
                                    id="permission_{{ $item->id }}"
                                    name="permissions[]"
                                    value="{{ $item->id }}"
                                    {{ in_array($item->id, $role->rolesAccessManagement->pluck('id')->toArray()) ? 'checked' : '' }}
                                />
                                <label class="form-check-label" for="permission_{{ $item->id }}">
                                    {{ $item->access }}
                                </label>
                            </div>
                            @empty
                            <div class="col-12">No permissions available</div>
                            @endforelse
                            @error('permissions')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                         

                    <div class="col-12 mb-2">
                        <h5>Permissions: <i class="fa fa-light fa-lock" aria-hidden="true"></i></h5>
                        <div class="col-6 p-0 d-flex flex-wrap">
                            @if($role->rolesAccessManagement->count())
                                @foreach ($role->rolesAccessManagement as $item)
                                    <h6 class="mr-1">
                                        <span class="badge badge-primary">
                                            {{ $item['access'] }}
                                        </span>
                                    </h6>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-12 mb-2">
                        <h5>Assigned users: <i class="fa-light fa-user"></i> </h5>
                        <div class="col-6 p-0 d-flex flex-wrap">
                            @if($role->userRoles->count())
                                @foreach ($role->userRoles as $item)
                                    <h6 class="mr-1">
                                        <span class="badge badge-primary">
                                            {{ $item->username }}
                                        </span>
                                    </h6>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="form-group col-12">
                        <button type="submit" class="btn btn-primary">
                            Save Changes
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript" src="{{ mix('js/roles_management/form.js') }}"></script>
@endpush
