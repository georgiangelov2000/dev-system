@extends('app')
@section('title', 'Categories')

@section('content')
<div id='categories-page'>
    <div class="container">
        <div class="row justify-content-between mb-3">
            <h3 class="mb-0">Categories</h3>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalForm">
                <i class="fa fa-plus"></i> Add Category
            </button>   
        </div>
        <div class="row">
            <div class="card col-12">
                <div class="card-body">
                </div>
            </div>
        </div>
    </div>
    @include('templates.modal_form_one',[
    'labelOne' => "name",
    'labelTwo' => "description",

    'inputOne' => "name[]",
    'inputTwo' => "description[]",

    'title'=>"Add Category",
    'isMultiple' => true,

    'formMethod' => "post",
    'formRoute' => route('category.store'),

    'formIdentificator' => "categoryForm"
    ])
    @endsection
    @push('scripts')
    <script type="text/javascript" src="{{mix('js/categories.js')}}"></script>
    @endpush