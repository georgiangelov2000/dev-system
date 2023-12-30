@extends('app')

@section('content')
    <div class="card card-default cardTemplate">
        <div class="card-header bg-primary">
            <div class="col-12">
                <h3 class="card-title">Create Role Managment</h3>
            </div>
        </div>
        <div class="card-body">
            <div class="col-12">
                <form action='{{ route('roles.store') }}' method='POST'>
                    @csrf
                    <div class="row flex-wrap">

                        <div class="col-12 d-flex">
                            <div class="form-group col-3">
                                <label class="form-label required" for="name">Role Name</label>
                                <input type="text" class="form-control @error('name')  is-invalid @enderror" id="name"
                                    name="name" value='{{ old('name') ? e(old('name')) : '' }}' placeholder="Enter name">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-3">
                                <label class="form-label" for="users">Assign users</label>
                                <select multiple class="form-control" name="users[]" id="users">
                                    @if($users->count())
                                        @foreach ($users as $item)
                                            <option value="{{ $item->id }}">{{ $item->username }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('users')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group col-6 d-flex flex-wrap align-items-center">
                                <label class="form-label required col-12 p-0" for="permissions">Permissions</label>
                                @if($access_management->count())
                                    @foreach ($access_management as $item)
                                        <div class="form-check mr-2 pr-2 d-flex align-items-center border border-right-1 border-left-0 border-top-0 border-bottom-0 ">
                                            <input 
                                                class="form-check-input mt-0" 
                                                type="checkbox" 
                                                id="permission_{{ $item->id }}" 
                                                name="permissions[]"
                                                value="{{ $item->id }}"
                                                @if ($item->selected)
                                                    checked
                                                @endif
                                            />
                                            <label class="form-check-label" for="permission_{{ $item->id }}">
                                                {{ $item->access }}
                                            </label>
                                        </div>
                                    @endforeach
                                @endif
                                @error('permissions')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
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