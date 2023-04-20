@extends('app')
@section('title', 'Preview')

@section('content')
    <div class="row justify-content-between mb-3">
        <div class="col-12 d-flex justify-content-between bg-white shadow-sm p-2 rounded">
            <h3 class="mb-0">Preview <span class="text-primary">{{$product->name}}</span></h3>
        </div>
    </div>
    <div class="row">
        <div class="card col-3">
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-6">
                        @if($product->images)
                            <img class="rounded mx-auto w-100 shadow bg-white rounded mb-2" src="{{$product->images->path}}{{$product->images->name}}">
                        @endif
                    </div>
                    <div class="col-12 d-flex justify-content-between border-bottom pl-0 pr-0 pt-2 pb-2">
                        <strong>Single price: </strong>
                        <span>{{$product->price}} <i class="font-weight-bold fa-light fa-euro-sign"></i></span>
                    </div>
                    <div class="col-12 d-flex justify-content-between border-bottom pl-0 pr-0 pt-2 pb-2">
                        <strong>Total price: </strong>
                        <span>{{$product->total_price}} <i class="font-weight-bold fa-light fa-euro-sign"></i></span>
                    </div>
                    <div class="col-12 d-flex justify-content-between border-bottom pl-0 pr-0 pt-2 pb-2">
                        <strong>Created at: </strong>
                        <span>{{$product->created_at->format('Y-m-d')}}</span>
                    </div>
                    <div class="col-12 d-flex justify-content-between border-bottom pl-0 pr-0 pt-2 pb-2">
                        <strong>Quantity</strong>
                        <span>{{$product->quantity}}</span>
                    </div>
                    <div class="col-12 d-flex justify-content-between border-bottom pl-0 pr-0 pt-2 pb-2">
                        <strong>Status: </strong>
                        <span>{{$product->status}}</span>
                    </div>
                    <div class="col-12 d-flex justify-content-between border-bottom pl-0 pr-0 pt-2 pb-2">
                        <strong>Code: </strong>
                        <span>{{$product->code}}</span>
                    </div>
                    <div class="col-12 d-flex justify-content-between border-bottom pl-0 pr-0 pt-2 pb-2">
                        <strong>Note: </strong>
                        <span class="w-50 text-right">{{$product->notes}}</span>
                    </div>
                    <div class="col-12 d-flex justify-content-between border-bottom pl-0 pr-0 pt-2 pb-2">
                        <strong>Supplier: </strong>
                        <a href="{{route('supplier.edit',$product->supplier->id)}}">
                            {{$product->supplier->name}}
                        </a>
                    </div>
                    <div class="col-12 d-flex justify-content-between border-bottom pl-0 pr-0 pt-2 pb-2">
                        <strong>Brands: </strong>
                        @if(count($product->brands))
                                @php
                                    $relatedBrads = $product->brands;   
                                @endphp

                                @foreach ( $relatedBrads as $item)
                                    <span>{{$item->name}}</span>  
                                @endforeach
                        @endif
                    </div>
                    <div class="col-12 d-flex justify-content-between border-bottom pl-0 pr-0 pt-2 pb-2">
                        <strong>Categories: </strong>
                        @if(count($product->categories))
                                @php
                                    $relatedCategories = $product->categories;   
                                @endphp
                                <div class="w-50 text-right">
                                    @foreach ($relatedCategories as $item)
                                        <span>{{$item->name}}</span>  
                                    @endforeach
                                </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    @endsection

    