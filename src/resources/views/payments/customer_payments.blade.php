@extends('app')
@section('title', 'Customer payments')

@section('content')
    <div class="row justify-content-between mb-3">
        <div class="col-12 d-flex justify-content-between">
            <h3 class="mb-0">Customer payments</h3>
        </div>
    </div>
    <div class="row">
        <div class="card col-12 cardTemplate">
            <div class="card-body">
                <form action="{{route('payment.check')}}" method="POST" id="filterForm">
                    @csrf
                    <div class="form-row">
                        <div class="col-3">
                            <label for="customRange1">Select Customer</label>
                            <select class="form-control selectCustomer" name="customer">
                                <option value="0">Nothing selected</option>
                                @foreach ($customers as $item)
                                    <option value="{{$item->id}}">{{$item->name}}</option>                                    
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3 dateRange">
                            <label for="customRange1">Date range</label>
                            <div class="d-flex align-items-center">
                                <input type="text" class="form-control pull-right" name="datetimes" />
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="col mb-2">
                                <label></label>
                            </div>
                            <button title="Filter" class="btn btn-primary filter" type="submit">
                                <i class="fa-light fa-magnifying-glass"></i>
                            </button>
                        </div>
                    </div>
                </form>
                <div class="form-row">
                    <div class="col-12 d-flex mt-2">
                        <label for="">Disabled Date range: </label>
                        <div class="form-check ml-2">
                            <input class="form-check-input disabledDateRange" type="checkbox">
                            <label class="form-check-label"></label>
                        </div>
                    </div>
                    <div id="loader" class="spinner-border text-dark" role="status" style="display: none;">
                        <span class="sr-only">Loading...</span>
                    </div> 
                </div>
            </div>
        </div>
        <div id="paymentTemplate" class="col-12 p-0"></div>
    </div>

    @push('scripts')
        <script type="text/javascript" src="{{ mix('js/payments/customer_payments.js') }}"></script>
    @endpush

@endsection
