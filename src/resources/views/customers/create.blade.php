@extends('app')

@section('content')
    <div class="card card-default cardTemplate">
        <div class="card-header">
            <div class="col-12">
                <h3 class="card-title">Create customer</h3>
            </div>
        </div>
        <div class="card-body">
            <form class="d-flex flex-wrap" action='{{ route('customer.store') }}' method='POST' enctype="multipart/form-data">
                @csrf
                <div class="row flex-wrap">
                    <div class="col-10 d-flex flex-wrap p-2">
                        <div class="form-group col-12">
                            <div style="height:30px">
                                <label for="image">File</label>
                            </div>
                            <div class="custom-file col-12">
                                <input type="file" class="custom-file-input" name="image" id="image">
                                <label class="custom-file-label" for="customFile">Choose file</label>
                                @error('image')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group col-6">
                            <label for="name">Name</label>
                            <input type="text" class="form-control @error('name')  is-invalid @enderror" id="name"
                                name="name" value='{{ old('name') ? e(old('name')) : '' }}' placeholder="Enter name">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-6">
                            <label for="email">Email</label>
                            <input type="email"class="form-control @error('email')  is-invalid @enderror" id="email"
                                name="email" value='{{ old('email') ? e(old('email')) : '' }}' placeholder="Enter email">
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-6">
                            <label for="phone">Phone</label>
                            <input type="text" class="form-control @error('phone')  is-invalid @enderror" id="phone"
                                name="phone" value='{{ old('phone') ? e(old('phone')) : '' }}' placeholder="Enter phone">
                            @error('phone')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-6">
                            <label for="website">Website</label>
                            <input type="ztext" class="form-control @error('website')  is-invalid @enderror"
                                id="website" name="website" value='{{ old('website') ? e(old('website')) : '' }}'
                                placeholder="Enter website">
                            @error('website')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-6">
                            <label for="zip">Zip Code</label>
                            <input type="text" class="form-control @error('zip')  is-invalid @enderror" id="zip"
                                name="zip" value='{{ old('zip') ? e(old('zip')) : '' }}' placeholder="Enter zip">
                            @error('zip')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-6">
                            <label for="country">Country</label>
                            <select class="form-control selectCountry" id="country" name="country_id"
                                @error('country_id') data-style="border-danger"  is-invalid @enderror>
                                <option value="">Select country</option>
                                @foreach ($countries as $country)
                                    <option data-country="{{ $country->name }}" value="{{ $country->id }}">
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('state_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-6">
                            <label for="state_id">State</label>
                            <select id="state" name="state_id"
                                @error('state_id') data-style="border-danger"  is-invalid @enderror
                                class="form-control selectState">
                                <option value="">Select a state</option>
                            </select>
                            @error('state_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-6">
                            <label for="address">Address</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa-light fa-location-dot"></i></span>
                                </div>
                                <input value="{{ old('address') ? e(old('address')) : '' }}" type="text" id="address"
                                    name="address" class="form-control" placeholder="Enter address" />
                                <span class="input-group-append">
                                    <button type="button" id="searchAddress" class="btn btn-info btn-flat">Search</button>
                                </span>
                            </div>
                            @error('address')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="addresses col-6"></div>
                        <div class="form-group col-12">
                            <label for="country">Notes</label>
                            <textarea maxlength="255" class="form-control @error('notes')  is-invalid @enderror" class="form-control" name="notes"
                                value='{{ old('notes') ? e(old('notes')) : '' }}'></textarea>
                            @error('notes')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div id="mapWrapper" class="col-4 mb-2">
                            <div id="map-container">
                                <div id="map"></div>
                            </div>
                        </div>
                        <div class="form-group col-12">
                            <button type="submit" class="btn btn-primary">
                                Save changes
                            </button>
                        </div>
                    </div>
                    <div class="col-2 p-2">
                        <div class="row w-100 mb-2">
                            <div class="col-12 d-none imagePreview">
                                <div class="position-relative">
                                    <img id="preview-image" alt="Preview"
                                        class="w-100 h-100 m-0">
                                    <div class="ribbon-wrapper ribbon-lg">
                                        <div class="ribbon bg-success text-lg">
                                            Preview
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script type="text/javascript" src="{{ mix('js/customers/form.js') }}"></script>
    <script type="text/javascript">
        let STATE_ROUTE = "{{ route('state', ':id') }}";
    </script>
@endpush
