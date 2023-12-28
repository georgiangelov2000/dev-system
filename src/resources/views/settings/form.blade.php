@extends('app')

@section('content')
    <div class="card card-default cardTemplate">
        <div class="card-header bg-primary">
            <div class="col-12">
                <h3 class="card-title">Settings</h3>
            </div>
        </div>
        <div class="card-body">
            <form class="row" method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="col-5 col-sm-3">
                    <div class="nav flex-column nav-tabs h-100" id="company_settings-tab" role="tablist"
                        aria-orientation="vertical">
                        <a class="nav-link active" id="vert-tabs-company_settings-tab" data-toggle="pill"
                            href="#vert-tabs-company_settings" role="tab" aria-controls="vert-tabs-company_settings"
                            aria-selected="true">
                            Company Configuration
                        </a>
                        <a class="nav-link" id="vert-tabs-server-configuration-tab" data-toggle="pill"
                            href="#vert-tabs-server-configuration" role="tab"
                            aria-controls="vert-tabs-server-configuration" aria-selected="false">
                            Server information
                        </a>
                        <a class="nav-link" id="vert-tabs-email-sender-configuration-tab" data-toggle="pill"
                            href="#vert-tabs-email-sender-configuration" role="tab"
                            aria-controls="vert-tabs-email-sender-configuration" aria-selected="false">
                            Email Sender
                        </a>
                    </div>
                </div>

                <div class="col-7 col-sm-9">
                    <div class="tab-content" id="vert-tabs-tabContent">

                        <div class="tab-pane text-left fade show active" id="vert-tabs-company_settings" role="tabpanel"
                            aria-labelledby="vert-tabs-company_settings-tab">
                            <div class="row d-flex flex-wrap p-2">
                                <input type="hidden" name="type" value="1" />

                                <div class="col-6 d-flex flex-wrap align-content-start">
                                    <h5 class="col-12">File uploading</h5>
                                    <div class="col-12">
                                        <hr class="mt-0 mb-3">
                                    </div>
                                    <div class="form-group col-12 mb-0">
                                        <div style="height:30px">
                                            <label for="image">File</label>
                                        </div>
                                        <div class="custom-file col-12">
                                            <input type="file" name="image" id="image" class="custom-file-input">
                                            <label class="custom-file-label" for="customFile">Choose file</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6 d-flex flex-wrap justify-content-between">
                                    <h5 class="col-12">Main information</h5>
                                    <div class="col-12">
                                        <hr class="mt-0 mb-3">
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="comapny-name">Company name</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fa-light fa-building"></i></span>
                                            </div>
                                            <input value="{{ $settings['name'] ?? '' }}" type="text" name="name"
                                                id="comapany-name" class="form-control" placeholder="Enter settings name" />
                                            @error('name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="name">Email</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fa-light fa-envelope"></i></span>
                                            </div>
                                            <input type="email" value="{{ $settings['email'] ?? '' }}" id="email"
                                                name="email" class="form-control" placeholder="Enter e-mail" />
                                            @error('email')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="tax_number">Tax number</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i
                                                        class="fa-light fa-note-sticky"></i></span>
                                            </div>
                                            <input value="{{ $settings['tax_number'] }}" type="text"
                                                id="tax_number" name="tax_number" class="form-control"
                                                placeholder="Enter tax number" />
                                            @error('tax_number')
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
                                            <input id="registration_date"
                                                value="{{ $settings['registration_date'] ?? '' }}" type="text"
                                                class="form-control datepicker" name="registration_date">
                                        </div>
                                        @error('registration_date')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-6 d-flex flex-wrap justify-content-between">
                                    <h5 class="col-12">Contacts</h5>
                                    <div class="col-12">
                                        <hr class="mt-0 mb-3">
                                    </div>
                                    <div class="form-group col-4">
                                        <label for="phone_number">Phone number</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i
                                                        class="fa-light fa-phone-flip"></i></span>
                                            </div>
                                            <input value="{{ $settings['phone_number'] ?? '' }}" type="text"
                                                name="phone_number" id="phone_number" class="form-control"
                                                placeholder="Enter phone number" />
                                            @error('phone_number')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group col-4">
                                        <label for="country">Country</label>
                                        <select class="form-control selectCountry" name="country" id="country">
                                            <option value="">Select option</option>
                                            @foreach ($data['countries'] as $item)
                                                <option {{ $settings['country'] === $item->name ? 'selected' : '' }}
                                                    value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('country')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-4">
                                        <label for="state">State</label>
                                        <select class="form-control selectState" name="state" id="state">
                                            @foreach ($data['states'] as $state)
                                                <option {{ $settings['state'] === $state->name ? 'selected' : '' }}
                                                    value="{{ $state->id }}">{{ $state->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('state')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="address">Address</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i
                                                        class="fa-light fa-location-dot"></i></span>
                                            </div>
                                            <input value="{{ $settings['address'] ?? '' }}" type="text"
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

                                    <div class="addresses"></div>
                                </div>

                                <div class="col-6 d-flex flex-wrap justify-content-between">
                                    <h5 class="col-12">Additional information</h5>
                                    <div class="col-12">
                                        <hr class="mt-0 mb-3">
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="owner_name">Owner</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fa-light fa-user"></i></span>
                                            </div>
                                            <input value="{{ $settings['owner_name'] }}" type="text"
                                                name="owner_name" id="owner_name" class="form-control"
                                                placeholder="Enter owner" />
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
                                            <input value="{{ $settings['website'] ?? '' }}" type="text"
                                                id="website" name="website" class="form-control"
                                                placeholder="Enter website" />
                                            @error('website')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="bussines_type">Bussines type</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i
                                                        class="fa-light fa-business-time"></i></span>
                                            </div>
                                            <input value="{{ $settings['bussines_type'] ?? '' }}" type="text"
                                                name="bussines_type" id="bussines_type" class="form-control"
                                                placeholder="Enter type of the bussines" />
                                            @error('bussines_type')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="tab-pane fade" id="vert-tabs-server-configuration" role="tabpanel"
                            aria-labelledby="vert-tabs-server-configuration-tab">
                            <label>PHP Version</label>
                            <p class="input-group-text col-3 border-0">
                                {{ $server_information['php_version'] }}
                            </p>
                            <label>Webserver</label>
                            <p class="input-group-text col-3 border-0">
                                {{ $server_information['server_software'] }}
                            </p>
                            <label>Protocol</label>
                            <p class="input-group-text col-3 border-0">
                                {{ $server_information['protocol'] }}
                            </p>
                            <label>Load average for the last 1 minute.</label>
                            <p class="input-group-text col-3 border-0">
                                {{ $server_information['server_load']['0'] }}
                                @if ($server_information['server_load']['0'] > $cpu_cores)
                                    <span class="text-danger"> High Load</span>
                                @else
                                    <span class="text-success ml-2"> Low Load</span>
                                @endif
                            </p>
                            <label>Load average for the last 5 minutes.</label>
                            <p class="input-group-text col-3 border-0">
                                {{ $server_information['server_load']['1'] }}
                                @if ($server_information['server_load']['1'] > $cpu_cores)
                                    <span class="text-danger"> High Load</span>
                                @else
                                    <span class="text-success ml-2"> Low Load</span>
                                @endif
                            </p>
                            <label>Load average for the last 15 minutes.</label>
                            <p class="input-group-text col-3 border-0">
                                {{ $server_information['server_load']['2'] }}
                                @if ($server_information['server_load']['2'] > $cpu_cores)
                                    <span class="text-danger"> High Load</span>
                                @else
                                    <span class="text-success ml-2"> Low Load</span>
                                @endif
                            </p>
                        </div>

                        <div class="tab-pane fade" id="vert-tabs-email-sender-configuration" role="tabpanel"
                            aria-labelledby="vert-tabs-email-sender-configuration-tab">
                            <div class="form-group col-5">  
                                <label for="notification_email">Notification email</label>
                                <input 
                                    type="notification_email" 
                                    name="notification_email" 
                                    value="{{ $settings['notification_email'] ?? '' }}"
                                    class="form-control" 
                                />
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-10 d-flex flex-wrap justify-content-between mt-2 pl-0">
                    <div class="form-group col-6 text-left">
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
    </div>

    @push('scripts')
        <script type="text/javascript" src="{{ mix('js/settings/settings.js') }}"></script>
        <script type="text/javascript">
            const LOCATION_API_ROUTE = "{{ route('api.location') }}";
        </script>
    @endpush
@endsection
