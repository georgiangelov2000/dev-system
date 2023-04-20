@extends('app')
@section('title', 'Edit supplier')

@section('content')
<div class="card card-default cardTemplate">
    <div class="card-header">
        <div class="col-12">
            <h3 class="card-title">Edit supplier</h3>
        </div>
    </div>
    <div class="card-body">
        <form class="d-flex flex-wrap" action='{{route('supplier.update',$supplier->id)}}' method='POST' enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="col-12 d-none imagePreview">
                <h5>Preview</h5>
                <img id="preview-image" class="img-thumbnail w-50">
            </div>
            <div class="form-group col-12">
                <img class="img-thumbnail w-25 h-100" 
                    src="{{$relatedRecords['image'] ? $relatedRecords['image']->path . $relatedRecords['image']->name : "https://leaveitwithme.com.au/wp-content/uploads/2013/11/dummy-image-square.jpg" }}"/>
            </div>
            <div class="col-6">
                <div style="height:30px">
                    <label for="image">File</label>
                </div>
                <div class="custom-file col-12">
                    <input 
                        type="file" 
                        class="custom-file-input" 
                        name="image" 
                        id="image"
                        accept="image/*"
                        value="{{$relatedRecords['image'] ? $relatedRecords['image']->path . $relatedRecords['image']->name : ''}}" 
                        >
                    <label class="custom-file-label" for="customFile">Choose file</label>
                    @error('image')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-group col-6"> 
                <label for="name">Name</label>
                <input 
                    type="text" 
                    class="form-control @error('name')  is-invalid @enderror" 
                    id="name" 
                    name="name" 
                    value='{{e($supplier->name)}}' 
                    placeholder="Enter name"
                    >
                @error('name')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group col-6"> 
                <label for="email">Email</label>
                <input 
                    type="email" 
                    class="form-control @error('email')  is-invalid @enderror" 
                    id="email" 
                    name="email" 
                    value='{{e($supplier->email)}}'
                    placeholder="Enter email"
                    >
                @error('email')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group col-6"> 
                <label for="phone">Phone</label>
                <input 
                    type="text" 
                    class="form-control @error('phone')  is-invalid @enderror" 
                    id="phone" 
                    name="phone" 
                    value='{{e($supplier->phone)}}'
                    placeholder="Enter phone"
                    >
                @error('phone')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group col-6"> 
                <label for="address">Address</label>
                <input 
                    type="text" 
                    class="form-control @error('address')  is-invalid @enderror" 
                    id="address" 
                    name="address" 
                    value='{{e($supplier->address)}}'
                    placeholder="Enter address"
                    >
                @error('address')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group col-6"> 
                <label for="website">Website</label>
                <input 
                    type="text" 
                    class="form-control @error('website')  is-invalid @enderror" 
                    id="website" 
                    name="website" 
                    value='{{e($supplier->website)}}' 
                    placeholder="Enter website"
                    >
                @error('website')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group col-6"> 
                <label for="zip">Zip Code</label>
                <input 
                    type="text" 
                    class="form-control @error('zip')  is-invalid @enderror" 
                    id="zip" 
                    name="zip" 
                    value='{{e($supplier->zip)}}' 
                    placeholder="Enter zip"
                    >
                @error('zip')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group col-6"> 
                <label for="country">Country</label>
                <select class="form-control selectCountry" id="country" name="country_id">
                    <option value="0" >Select country</option>
                    @foreach($countries as $country)
                    <option 
                        data-country="{{$country->name}}" 
                        value="{{$country->id}}"
                        {{$supplier->country_id === $country->id ? 'selected' : ''}}
                        >
                        {{$country->name}}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-6"> 
                <label for="country">State</label>
                <select id="state" name="state_id" class="form-control @error('state_id')  is-invalid @enderror selectState">
                    <option value="0" >Select state</option>
                    @foreach($states as $state)
                    <option 
                        data-country="{{$state->name}}" 
                        value="{{$state->id}}"
                        {{$supplier->state_id === $state->id ? 'selected' : ''}}
                        >
                        {{$state->name}}
                    </option>
                    @endforeach
                </select>
                @error('state_id')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group col-6">
                <label for="country">Categories</label>
                <select 
                    multiple="" 
                    class="form-control selectMultiple" 
                    name="categories[]"
                    data-actions-box="true" 
                    data-dropup-auto="false"
                    multiple data-selected-text-format="count > 5"
                >
                    @foreach($categories as $category)
                        <option 
                            value="{{$category->id}}"
                            {{ in_array($category->id, $relatedRecords['categories']) ? "selected" : "" }}
                        >{{$category->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-12"> 
                <label for="country">Notes</label>
                <textarea  
                    class="form-control @error('notes')  is-invalid @enderror" 
                    name="notes" 
                    >{{e($supplier->notes)}}</textarea>
                @error('notes')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group col-12"> 
                <button type="submit" class="btn btn-primary">
                    Save changes  
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript" src="{{ mix('js/suppliers/form.js') }}"></script>
<script>
    let STATE_ROUTE = "{{route('state',':id')}}";
</script>
@endpush

