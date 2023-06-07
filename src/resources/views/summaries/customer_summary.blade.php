@extends('app')
@section('title', 'Customer summary')

@section('content')
    <div class="row">
        <div class="card col-12 cardTemplate">
            <div class="card-header">
                <div class="col-12">
                    <h3 class="card-title">
                       Summary
                    </h3>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-12">
                        <h6 class="font-weight-bold">Legend:</h6>
                    </div>
                    <div class="col-12">
                        Total sales
                        <span>-</span>
                        <span>The total sum of sales for each package and status</span>
                    </div>
                    <div class="col-12">
                        Paid sales
                        <span>-</span>
                        <span>Sales that have been paid by the customer and received</span>
                    </div>
                </div>                  
                <form action="" id="filterForm">
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
                        <div class="col-3">
                            <label for="order_filter">Order filters (optional)</label>
                            <select class="form-control orderFilter"  name="order_filter" id="order_filter">
                                <option value="0">All</option>
                                <option value="1">Paid</option>
                                <option value="2">Not Paid</option>
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
                <div id="summary-container"></div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script type="text/javascript">
            let SUMMARY = "{{route('summary.take.customer')}}";
            let PREVIEW_ROUTE = "{{ route('purchase.preview',':id') }}";
        </script>
        <script type="text/javascript" src="{{ mix('js/summaries/customer_summary.js') }}"></script>
    @endpush

@endsection
