@extends('app')

@section('content')
    <div class="card card-default cardTemplate">
        <div class="card-header">
            <div class="col-12">
                <h3 class="card-title">Company information</h3>
            </div>
        </div>
        <div class="card-body">

            <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row d-flex flex-wrap p-2">
                    <input
                        type="hidden" 
                        name="type" 
                        value="1"
                    />

                    <div class="col-10 d-flex flex-wrap mb-2">
                        <div class="form-group col-6 mb-0">
                            <div style="height:30px">
                                <label for="image">File</label>
                            </div>
                            <div class="custom-file col-12">
                                <input type="file" name="image" id="image" class="custom-file-input">
                                <label class="custom-file-label" for="customFile">Choose file</label>
                            </div>
                        </div>

                        <div class="form-group col-6">
                            <label for="name">Email</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa-light fa-envelope"></i></span>
                                </div>
                                <input type="email" value="{{ $settings['email'] ?? '' }}" id="email" name="email"
                                    class="form-control" placeholder="Enter e-mail" />
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group col-6">
                            <label for="country_id">Country</label>
                            <select class="form-control selectCountry" name="country_id" id="country_id">
                                <option value="">Select option</option>
                                @foreach ($data['countries'] as $item)
                                    <option {{ $settings['country'] === $item->name ? 'selected' : '' }}
                                        value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                            @error('country_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-6">
                            <label for="state_id">State</label>
                            <select class="form-control selectState" name="state_id" id="state_id">
                                @foreach ($data['states'] as $state)
                                    <option {{ $settings['state'] === $state->name ? 'selected' : '' }}
                                        value="{{ $state->id }}">{{ $state->name }}</option>
                                @endforeach
                            </select>
                            @error('state_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-6">
                            <label for="comapny-name">Company name</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa-light fa-building"></i></span>
                                </div>
                                <input value="{{ $settings['name'] ?? '' }}" type="text" name="name" id="comapany-name"
                                    class="form-control" placeholder="Enter settings name" />
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group col-6">
                            <label for="phone_number">Phone number</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa-light fa-phone-flip"></i></span>
                                </div>
                                <input value="{{ $settings['phone_number'] ?? '' }}" type="text" name="phone_number"
                                    id="phone_number" class="form-control" placeholder="Enter phone number" />
                                @error('phone_number')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group col-6">
                            <label for="tax_number">Tax number</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa-light fa-note-sticky"></i></span>
                                </div>
                                <input value="{{ $settings['tax_number'] ?? '' }}" type="text" id="tax_number"
                                    name="tax_number" class="form-control" placeholder="Enter tax number" />
                                @error('tax_number')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group col-6">
                            <label for="owner_name">Owner</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa-light fa-user"></i></span>
                                </div>
                                <input value="{{ $settings['owner_name'] ?? '' }}" type="text" name="owner_name"
                                    id="owner_name" class="form-control" placeholder="Enter owner" />
                                @error('owner_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group col-6">
                            <label for="website">Website</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa-light fa-globe"></i></span>
                                </div>
                                <input value="{{ $settings['website'] ?? '' }}" type="text" id="website"
                                    name="website" class="form-control" placeholder="Enter website" />
                                @error('website')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group col-6">
                            <label for="bussines_type">Bussines type</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa-light fa-business-time"></i></span>
                                </div>
                                <input value="{{ $settings['bussines_type'] ?? '' }}" type="text" name="bussines_type"
                                    id="bussines_type" class="form-control" placeholder="Enter type of the bussines" />
                                @error('bussines_type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group col-6">
                            <label for="registration_date">Registration date</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="far fa-calendar-alt"></i>
                                    </span>
                                </div>
                                <input id="registration_date" value="{{ $settings['registration_date'] ?? '' }}"
                                    type="text" class="form-control datepicker" name="registration_date">
                            </div>
                            @error('registration_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-6">
                            <label for="address">Address</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa-light fa-location-dot"></i></span>
                                </div>
                                <input value="{{ $settings['address'] ?? '' }}" type="text" id="address"
                                    name="address" class="form-control" placeholder="Enter address" />
                                <span class="input-group-append">
                                    <button type="button" id="searchAddress"
                                        class="btn btn-primary btn-flat">Search</button>
                                </span>
                            </div>
                            @error('address')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="addresses"></div>
                    </div>
                    <div class="col p-2">
                        <div class="row w-100">
                            <div class="col-12">
                                @if ($settings)
                                    @if ($settings['image_path'])
                                        <img class="cardWidgetImage w-100 m-0" src="{{ $settings['image_path'] }}" />
                                    @else
                                        <img id="settingsImage" class="w-100 m-0"
                                            src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png" />
                                    @endif
                                @else
                                    <img id="settingsImage" class="w-100 m-0"
                                        src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png" />
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-10 d-flex flex-wrap justify-content-between">
                        <div class="form-group col-6 text-left">
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                        <div class="form-group col-6 text-right">
                            <button id="print" class="btn btn-outline-primary" type="button">
                                <i class="fa-light fa-file-pdf fa-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script type="text/javascript" src="{{ mix('js/settings/settings.js') }}"></script>
        <script type="text/javascript">
            const LOCATION_API_ROUTE = "{{ route('api.location') }}";
        </script>
    @endpush
@endsection
