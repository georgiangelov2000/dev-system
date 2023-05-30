@extends('app')
@section('title', 'Company settings')

@section('content')
    <div class="card card-default cardTemplate">
        <div class="card-header">
            <div class="col-12">
                <h3 class="card-title">Company settings information</h3>
            </div>
        </div>
        <div class="card-body">

            <div class="col-12">
                <form method="POST" action="{{ route('settings.company.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">

                        <div class="col-12 mb-2">
                            <div class="col-3">
                                @if ($company->image_path)
                                    <img id="companyImage" class="card card-widget widget-user w-100 m-0"
                                        src="{{ $company->image_path }}" />
                                @else
                                    <img id="companyImage" class="w-100 m-0"
                                        src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png" />
                                @endif
                            </div>
                        </div>
                        <div class="col-12 d-flex">

                            <div class="form-group col-3 mb-0">
                                <div style="height:30px">
                                    <label for="image">File</label>
                                </div>
                                <div class="custom-file col-12">
                                    <input type="file" name="image" id="image" class="custom-file-input">
                                    <label class="custom-file-label" for="customFile">Choose file</label>
                                </div>
                            </div>

                            <div class="form-group col-3">
                                <label for="name">Email</label>
                                <input type="email" value="{{ $company->email }}" id="email" name="email"
                                    class="form-control" placeholder="Enter e-mail" />
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-3">
                                <label for="country_id">Country</label>
                                <select class="form-control selectCountry" name="country_id" id="country_id">
                                    <option value="">Select option</option>
                                    @foreach ($countries as $item)
                                        <option {{ $company->country_id === $item->id ? 'selected' : '' }}
                                            value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                @error('country_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-3">
                                <label for="state_id">State</label>
                                <select class="form-control selectState" name="state_id" id="state_id">
                                    @foreach ($states as $state)
                                        <option {{$state->id  === $company->state_id ? 'selected' : ''}} value="{{$state->id}}">{{$state->name}}</option>
                                    @endforeach 
                                </select>
                                @error('state_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 d-flex">
                            <div class="form-group col-3">
                                <label for="comapny-name">Company name</label>
                                <input value="{{ $company->name }}" type="text" name="name" id="comapany-name"
                                    class="form-control" placeholder="Enter comapny name" />
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-3">
                                <label for="phone_number">Phone number</label>
                                <input value="{{ $company->phone_number }}" type="text" name="phone_number"
                                    id="phone_number" class="form-control" placeholder="Enter phone number" />
                                @error('phone_number')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-3">
                                <label for="tax_number">Tax number</label>
                                <input value="{{ $company->tax_number }}" type="text" id="tax_number" name="tax_number"
                                    class="form-control" placeholder="Enter tax number" />
                                @error('tax_number')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-3">
                                <label for="owner_name">Owner</label>
                                <input value="{{ $company->owner_name }}" type="text" name="owner_name" id="owner_name"
                                    class="form-control" placeholder="Enter owner" />
                                @error('owner_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        <div class="col-12 d-flex">

                            <div class="form-group col-3">
                                <label for="website">Website</label>
                                <input value="{{ $company->website }}" type="text" id="website" name="website"
                                    class="form-control" placeholder="Enter website" />
                                @error('website')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-3">
                                <label for="bussines_type">Bussines type</label>
                                <input value="{{ $company->bussines_type }}" type="text" name="bussines_type"
                                    id="bussines_type" class="form-control" placeholder="Enter type of the bussines" />
                                @error('bussines_type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-6">
                                <label for="address">Address</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa-light fa-location-dot"></i></span>
                                    </div>
                                    <input value="{{ $company->address }}" type="text" id="address" name="address"
                                        class="form-control" placeholder="Enter address" />
                                    <span class="input-group-append">
                                        <button type="button" id="searchAddress" class="btn btn-info btn-flat">Search</button>
                                    </span>
                                </div>
                                @error('address')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="addresses"></div>

                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    @push('scripts')
        <script type="text/javascript" src="{{ mix('js/settings/settings.js') }}"></script>
        <script type="text/javascript">
            const STATE_ROUTE = "{{ route('state', ':id') }}";
        </script>
    @endpush

@endsection
