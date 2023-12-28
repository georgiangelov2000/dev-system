@extends('app')

@section('content')
    <div class="card card-default cardTemplate">
        <div class="card-header bg-primary">
            <div class="col-12">
                <h3 class="card-title">Create customer</h3>
            </div>
        </div>
        <div class="card-body">
            <form class="d-flex flex-wrap" action='{{ route('customer.store') }}' method='POST' enctype="multipart/form-data">
                @csrf
                <div class="row flex-wrap">
                    <div class="col-10 d-flex flex-wrap p-2">

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
                                    <label class="custom-file-label" id="fileLabel" for="customFile">Choose file</label>
                                    @error('image')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-12 d-flex flex-wrap p-0">
                            
                            <h5 class="col-12 pt-2">Main Information</h5>
                            <div class="col-12">
                                <hr class="mt-0">
                            </div>
                            
                            <div class="form-group col-3">
                                <label class="form-label required" for="name">Name</label>
                                <input type="text" class="form-control @error('name')  is-invalid @enderror" id="name"
                                    name="name" value='{{ old('name') ? e(old('name')) : '' }}' placeholder="Enter name">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="form-group col-3">
                                <label class="form-label required" for="email">Email</label>
                                <input type="email"class="form-control @error('email')  is-invalid @enderror" id="email"
                                    name="email" value='{{ old('email') ? e(old('email')) : '' }}' placeholder="Enter email">
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-3">
                                <label for="website">Website</label>
                                <input type="ztext" class="form-control @error('website')  is-invalid @enderror"
                                    id="website" name="website" value='{{ old('website') ? e(old('website')) : '' }}'
                                    placeholder="Enter website">
                                @error('website')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12 d-flex flex-wrap p-0">
                            <h5 class="col-12 pt-2">Contacts</h5>
                            <div class="col-12">
                                <hr class="mt-0">
                            </div>
                            <div class="form-group col-3">
                                <label class="form-label required" for="country">Country</label>
                                <select data-live-search="true" class="form-control selectCountry" id="country" name="country_id"
                                    @error('country_id') data-style="border-danger"  is-invalid @enderror>
                                </select>
                                @error('state_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-3">
                                <label class="form-label required" for="state_id">State</label>
                                <select data-live-search="true" id="state" name="state_id"
                                    @error('state_id') data-style="border-danger"  is-invalid @enderror
                                    class="form-control selectState">
                                </select>
                                @error('state_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-3">
                                <label for="phone">Phone</label>
                                <input type="text" class="form-control @error('phone')  is-invalid @enderror" id="phone"
                                    name="phone" value='{{ old('phone') ? e(old('phone')) : '' }}' placeholder="Enter phone">
                                @error('phone')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-6">
                                <label class="form-label required" for="address">Address</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa-light fa-location-dot"></i></span>
                                    </div>
                                    <input value="{{ old('address') ? e(old('address')) : '' }}" type="text" id="address"
                                        name="address" class="form-control" placeholder="Enter address" />
                                    <span class="input-group-append">
                                        <button type="button" id="searchAddress" class="btn btn-primary btn-flat rounded-right">Search</button>
                                    </span>
                                </div>
                                @error('address')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-3">
                                <label class="form-label required" for="zip">Zip Code</label>
                                <input type="text" class="form-control @error('zip')  is-invalid @enderror" id="zip"
                                    name="zip" value='{{ old('zip') ? e(old('zip')) : '' }}' placeholder="Enter zip">
                                @error('zip')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="addresses col-12"></div>
                        </div>

                        <div class="col-12 d-flex flex-wrap p-0">
                            <h5 class="col-12 pt-2">Notes</h5>
                            <div class="col-12">
                                <hr class="mt-0">
                            </div>
                            <div class="form-group col-3">
                                <label for="country">Notes</label>
                                <textarea maxlength="255" class="form-control @error('notes')  is-invalid @enderror" class="form-control" name="notes"
                                    value='{{ old('notes') ? e(old('notes')) : '' }}'></textarea>
                                @error('notes')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group col-12">
                            <button type="submit" class="btn btn-primary">
                                Save changes
                            </button>
                        </div>
                    </div>
                    <div class="col-2 p-2">
                        <div class="row w-100 mt-4">
                            <div class="col-12">
                                <img name="cardWidgetImage" class="rounded w-100 m-0" src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png" />
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>

@push('scripts')
    <script type="text/javascript" src="{{ mix('js/customers/form.js') }}"></script>
    <script type="text/javascript">
        const LOCATION_API_ROUTE = "{{ route('api.location') }}";
        const COUNTRY_API_ROUTE = "{{ route('api.countries') }}";
        const CATEGORY_API_ROUTE = "{{ route('api.categories') }}";
    </script>
@endpush

@endsection