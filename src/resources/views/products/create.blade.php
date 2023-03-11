@extends('app')
@section('title', 'Add product')

@section('content')
<div class="card card-default cardTemplate">
    <div class="card-header">
        <div class="col-12">
            <h3 class="card-title">Create product</h3>
        </div>
    </div>
    <div class="card-body">
        <form class="d-flex flex-wrap" action='{{route('supplier.store')}}' method='POST' enctype="multipart/form-data">
            @csrf
            <div class="form-group col-6">
                <label for="image">Choose Image:</label>
                <input type="file" name="image" id="image" class="form-control">
            </div>
            <div class="form-group col-6"> 
                <label for="name">Name</label>
                <input type="text" class="form-control @error('name')  is-invalid @enderror" id="name" name="name" value='{{ old("name") ? e(old("name")) : '' }}' placeholder="Enter name">
                @error('name')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group col-6"> 
                <label for="quantity">Quantity</label>
                <input type="number" placeholder="Enter quantity" class="form-control @error('quantity')  is-invalid @enderror" id="quantity" name="quantity" value='{{ old("quantity") ? e(old("quantity")) : '' }}'>
                @error('quantity')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group col-6"> 
                <label for="price">Price</label>
                <input type="text" class="form-control @error('price')  is-invalid @enderror" id="price" name="price" value='{{ old("price") ? e(old("price")) : '' }}' placeholder="Enter price">
                @error('name')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group col-6"> 
                <label for="name">Discount Percent</label>
                <input type="number" class="form-control @error('discount_percent')  is-invalid @enderror" id="discount_percent" name="discount_percent" value='{{ old("discount_percent") ? e(old("discount_percent")) : '' }}' placeholder="Enter discount percent">
                @error('name')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group col-6"> 
                <label for="name">Generate unique code</label>
                <input type="text" class="form-control @error('code')  is-invalid @enderror" id="code" name="code" value='{{ old("code") ? e(old("code")) : '' }}' placeholder="Generate code">
                @error('name')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group col-6">
                <label for="name">Start discount date</label>
                <div class="input-group date" id="reservationdate" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input datepicker" data-target="#reservationdate" name="start_date_discount">
                    <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa-solid fa-calendar-days"></i></div>
                    </div>
                </div>
            </div>
            <div class="form-group col-6">
                <label for="name">End discount date</label>
                <div class="input-group date" id="reservationdate" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input datepicker" name="end_date_discount" data-target="#reservationdate">
                    <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa-solid fa-calendar-days"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-6 p-0">
                <div class="col-12">
                    <div class="form-group">
                        <label>Suppliers</label>
                        <select class="form-control selectSupplier" name="supplier_id">
                            <option value='9999' >All</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label>Categories</label>
                        <select class="form-control selectCategory" multiple>
                            <option value="">All</option>
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label>Subcategories</label>
                        <select class="form-control selectSubCategory" multiple>
                            <option value="">All</option>
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label>Brands (not necessarily)</label>
                        <select class="form-control selectBrands" multiple>
                            <option value="">All</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group col-6">
                <label for="notes">Notes</label>
                <textarea cols="3" rows="11" class="form-control" name="notes"></textarea>
            </div>
        </form>
    </div>
</div>

@push("scripts")
    <script type="text/javascript" src="{{mix('js/products/form.js')}}"></script>
@endpush
@endsection
