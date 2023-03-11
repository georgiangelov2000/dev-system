@extends('app')
@section('title', 'Products')

@section('content')
<div class="row justify-content-between mb-3">
    <div class="col-12 d-flex justify-content-between">
        <h3 class="mb-0">Products</h3>
        <a class="btn btn-primary" href="{{route('product.create')}}"><i class="fa fa-plus"></i> Add product</a>
    </div>
</div>
<div class="row">
    <div class="card col-12 cardTemplate">
        <div class="card-body">
            <div class="row">
                
            </div>
        </div>
    </div>
</div>
@endsection