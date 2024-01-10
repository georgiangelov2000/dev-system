@extends('app')

@section('content')
    <div class="row flex-wrap">
        <form class="col-12 d-flex flex-wrap" id="orderForm" action="{{ route('user.update',$user->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf

            <div class="card card-default cardTemplate col-12">
                <div class="card-header bg-primary">
                    <div class="col-12">
                        <h3 class="card-title">Member details</h3>
                    </div>
                </div>
                <div class="card-body">

                    <div class="row flex-wrap">
                        <div class="col-8 d-flex flex-wrap">
                            <div class="col-12 d-flex flex-wrap p-0">
                                <h5 class="col-12 pt-2">File uploading</h5>
                                <div class="col-12">
                                    <hr class="mt-0">
                                </div>
                                <div class="form-group col-12 pb-0">
                                    <div style="height:30px">
                                        <label for="image">File</label>
                                    </div>
                                    <div class="custom-file col-12">
                                        <input type="file" class="custom-file-input" name="image" id="image">
                                        <label class="custom-file-label" for="customFile" id="fileLabel">Choose file</label>
                                        @error('image')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 d-flex flex-wrap p-0">
                                <h5 class="col-12 pt-2 ">Main Information</h5>
                                <div class="col-12">
                                    <hr class="mt-0">
                                </div>
                                <div class="form-group col-6">
                                    <label for="username">Username</label>
                                    <input value='{{ e($user->username) }}' class="form-control" type="text" id="username" name="username">
                                    @error('username')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-6">
                                    <label for="first_name">First name</label>
                                    <input value='{{ e($user->first_name) }}' class="form-control" type="text" id="first_name" name="first_name">
                                    @error('first_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-6">
                                    <label for="middle_name">Middle name</label>
                                    <input value='{{ e($user->middle_name) }}' class="form-control" type="text" id="middle_name" name="middle_name">
                                    @error('middle_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-6">
                                    <label for="last_name">Last name</label>
                                    <input value='{{ e($user->last_name) }}' class="form-control" type="text" id="last_name" name="last_name">
                                    @error('last_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-6">
                                    <label for="email">Email</label>
                                    <input value='{{ e($user->email) }}' class="form-control" type="email" name="email" id="email">
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-6">
                                    <label for="password">Password</label>
                                    <input class="form-control" type="password" name="password" id="password">
                                    @error('password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-6">
                                    <label for="confirm-password">Confirm password</label>
                                    <input class="form-control" type="password" name="confirm-password"
                                        id="confirm-password">
                                    @error('confirm-password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-6">
                                    <label for="role_id">Role</label>
                                    <select name="role_id" id="role_id" class="form-control">
                                        @foreach ($roles as $key => $role)
                                            <option {{$key == $user->role_id ? 'selected' : ''}} value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('role_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 d-flex flex-wrap p-0">
                                <h5 class="col-12 pt-2 ">Details</h5>
                                <div class="col-12">
                                    <hr class="mt-0">
                                </div>
                                <div class="form-group col-6">
                                    <label for="gender">Gender</label>
                                    <select name="gender" id="gender" class="form-control">
                                        {{ $user->gender }}
                                        @foreach (config('statuses.genders') as $key => $gender)
                                            <option {{ $user->gender === $key ? "selected" : "" }} value="{{ $key }}">{{ $gender }}</option>
                                        @endforeach
                                    </select>
                                    @error('gender')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-6">
                                    <label for="card_id">Card identificator</label>
                                        <input 
                                            value="{{ e($user->card_id) }}"
                                            type="text"
                                            id="card_id" 
                                            name="card_id"
                                            class="form-control" 
                                        />
                                        @error('card_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                </div>
                                <div class="form-group col-6 ">
                                    <label id="birth_date">Birth date:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-calendar-alt"></i>
                                            </span>
                                        </div>
                                        <input 
                                            type="text" 
                                            id="birth_date" 
                                            class="form-control datepicker"
                                            name="birth_date"
                                            value="{{$user->birth_date}}"
                                        />
                                        @error('birth_date')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 d-flex flex-wrap p-0">
                                <h5 class="col-12 pt-2">Contacts</h5>
                                <div class="col-12">
                                    <hr class="mt-0">
                                </div>
                                <div class="form-group col-6">
                                    <label for="phone">Phone</label>
                                    <input value="{{ e($user->phone) }}" class="form-control" type="text" name="phone" id="phone">
                                    @error('phone')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-6">
                                    <label for="address">Address</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa-light fa-location-dot"></i></span>
                                        </div>
                                        <input 
                                            value="{{ e($user->address) }}"
                                            type="text"
                                            id="address" name="address" class="form-control"
                                            placeholder="Enter address" />
                                        <span class="input-group-append">
                                            <button type="button" id="searchAddress"
                                                class="btn btn-primary btn-flat rounded-right">Search</button>
                                        </span>
                                    </div>
                                    @error('address')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="addresses col-12"></div>

                            </div>
                        </div>
                        <div class="col-4 d-flex flex-wrap align-self-baseline">
                            <h5 class="col-12 pt-2">Contract PDF uploading</h5>
                            <div class="col-12">
                                <hr class="mt-0">
                            </div>
                            <div class="form-group col-12">
                                <div style="height:30px">
                                    <label for="pdf">PDF</label>
                                </div>
                                <div class="custom-file col-12">
                                    <input type="file" class="custom-file-input" name="pdf" id="pdf">
                                    <label class="custom-file-label" for="customFile" id="fileLabel">Choose file</label>
                                    @error('pdf')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <br>
                                <br>
                                @if(!$user->pdf_file_path)
                                    <div class="col-8 m-auto">
                                        <img 
                                            class="w-100" 
                                            id="pdfMockUpImage"
                                            src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/87/PDF_file_icon.svg/1200px-PDF_file_icon.svg.png"
                                            alt=""
                                        >
                                    </div>
                                @else
                                    <div id="pdfPreviewContainer" style="margin-top: 20px;">
                                        <object 
                                            data="{{ $user->pdf_file_path }}" 
                                            id="pdfPreview" 
                                            type="application/pdf" 
                                            style="width: 100%; height: 600px;"
                                        ></object>
                                    </div>
                                @endif
                                    <div 
                                        id="pdfPreviewContainer"
                                        style="display:none; margin-top: 20px;"
                                    >
                                        <object id="pdfPreview" type="application/pdf"style="width: 100%; height: 600px;"></object>
                                    </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-12">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script type="text/javascript" src="{{ mix('js/users/form.js') }}"></script>
    @endpush
@endsection
