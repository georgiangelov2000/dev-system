@extends('app')

@section('content')
    <div class="row flex-wrap">
        <form class="col-12 d-flex flex-wrap" id="orderForm" action="{{ route('user.store') }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <div class="card card-default cardTemplate col-6 mr-1">
                <div class="card-header">
                    <div class="col-12">
                        <h3 class="card-title">Member details</h3>
                    </div>
                </div>
                <div class="card-body">

                    <div class="col-12">
                        <div class="row flex-wrap">
                            <div class="col-6">
                                <div class="form-group col-12">
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
                                <div class="form-group col-12">
                                    <label for="username">Username</label>
                                    <input class="form-control" type="text" name="username" id="username">
                                    @error('username')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-12">
                                    <label for="middle_name">Middle name</label>
                                    <input class="form-control" type="text" id="middle_name" name="middle_name">
                                    @error('middle_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-12">
                                    <label for="password">Password</label>
                                    <input class="form-control" type="password" name="password" id="password">
                                    @error('password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-12">
                                    <label for="gender">Gender</label>
                                    <select name="gender" id="gender" class="form-control">
                                        <option value="">Nothing selected</option>
                                        @foreach (config('statuses.genders') as $key => $gender)
                                            <option value="{{ $key }}">{{ $gender }}</option>
                                        @endforeach
                                    </select>
                                    @error('gender')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-12">
                                    <label for="address">Address</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa-light fa-location-dot"></i></span>
                                        </div>
                                        <input value="{{ old('address') ? e(old('address')) : '' }}" type="text"
                                            id="address" name="address" class="form-control"
                                            placeholder="Enter address" />
                                        <span class="input-group-append">
                                            <button type="button" id="searchAddress"
                                                class="btn btn-primary btn-flat">Search</button>
                                        </span>
                                    </div>
                                    @error('address')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div id="mapWrapper" class="form-group col-12 mb-2">
                                    <div id="map-container">
                                        <div id="map"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group col-12">
                                    <label for="email">Email</label>
                                    <input class="form-control" type="email" name="email" id="email">
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-12">
                                    <label for="first_name">First name</label>
                                    <input class="form-control" type="text" id="first_name" name="first_name">
                                    @error('first_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-12">
                                    <label for="last_name">Last name</label>
                                    <input class="form-control" type="text" id="last_name" name="last_name">
                                    @error('last_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-12">
                                    <label for="confirm-password">Confirm password</label>
                                    <input class="form-control" type="password" name="confirm-password"
                                        id="confirm-password">
                                    @error('confirm-password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-12">
                                    <label for="role_id">Role</label>
                                    <select name="role_id" id="role_id" class="form-control">
                                        <option value="">Nothing selected</option>
                                        @foreach (config('statuses.roles') as $key => $role)
                                            <option value="{{ $key }}">{{ $role }}</option>
                                        @endforeach
                                    </select>
                                    @error('role_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-12">
                                    <label for="phone">Phone</label>
                                    <input class="form-control" type="text" name="phone" id="phone">
                                    @error('phone')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-12 ">
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
                                        />
                                        @error('birth_date')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group col-12">
                                    <label for="">Card identificator</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa-light fa-credit-card"></i></span>
                                        </div>
                                        <input 
                                            value="{{ old('card_id') ? e(old('card_id')) : '' }}" 
                                            type="text"
                                            id="card_id" 
                                            name="card_id" 
                                            class="form-control"
                                        />
                                        @error('card_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="addresses col-12"></div>
                            </div>
                            <div class="form-group col-12">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card card-default cardTemplate col-5 ml-2">
                <div class="card-header">
                    <div class="col-12">
                        <h3 class="card-title">Contract details</h3>
                    </div>
                </div>
                <div class="card-body">

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
                        <div class="col-8 m-auto">
                            <img class="w-100" id="pdfMockUpImage"
                                src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/87/PDF_file_icon.svg/1200px-PDF_file_icon.svg.png"
                                alt="">
                        </div>
                        <div id="pdfPreviewContainer" style="display:none" style="display:none; margin-top: 20px;">
                            <object id="pdfPreview" type="application/pdf" style="width: 100%; height: 600px;"></object>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script type="text/javascript" src="{{ mix('js/users/form.js') }}"></script>
    @endpush
@endsection
