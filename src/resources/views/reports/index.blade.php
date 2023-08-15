@extends('app')

@section('content')
    @php
        $thisMonthStart = date('Y-m-01');
        $thisMonthEnd = date('Y-m-t');
        $lastMonthStart = date('Y-m-01', strtotime('last month'));
        $lastMonthEnd = date('Y-m-t', strtotime('last month'));
    @endphp

    <div class="row">
        <div class="col-md-12">
            <div class="card col-12 cardTemplate">
                <div class="card-header">
                    <h3 class="card-title">Reports</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 d-flex flex-wrap">
                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info"><i class="fa-light fa-basket-shopping"></i></span>

                                    <div class="info-box-content">
                                        <p class="description pb-0 mb-0">Generate and export orders report by statuses:</p>
                                        <div class="btn-group">
                                            <button type="button" data-type_export="1" data-export="xls"
                                                class="btn btn-sm btn-success">Export XLS</button>
                                            <button type="button" data-type_export="1" data-export="csv"
                                                class="btn btn-sm btn-primary">Export CSV</button>
                                            <button type="button" data-type_export="1" data-export="pdf"
                                                class="btn btn-sm btn-danger">Export PDF</button>
                                        </div>
                                        <p class="description pb-0 mb-0">Generate for:</p>
                                        <div class="d-flex">
                                            <div class="form-check col-3">
                                                <input 
                                                    class="form-check-input" 
                                                    type="checkbox" 
                                                    value="{{$thisMonthStart}}-{{$thisMonthEnd}}"
                                                    id="orders_this_month">
                                                <label class="form-check-label" for="orders_this_month">
                                                    This month
                                                </label>
                                            </div>
                                            <div class="form-check col-3">
                                                <input 
                                                    class="form-check-input" 
                                                    type="checkbox" 
                                                    value="{{$lastMonthStart}}-{{$lastMonthEnd}}"
                                                    id="orders_last_month"
                                                >
                                                <label class="form-check-label" for="orders_last_month">
                                                    Last month
                                                </label>
                                            </div>
                                        </div>
                                        <div>
                                            <ul class="list-group mt-2 flex-row">
                                                <li class="d-flex list-group-item">
                                                    <span>All</span>
                                                    <div class="form-check ml-2">
                                                        <input name="checkbox" class="form-check-input" type="checkbox">
                                                    </div>
                                                </li>
                                                @foreach (config('statuses.payment_statuses') as $key => $status)
                                                    <li class="d-flex list-group-item">
                                                        <span>{{ $status }}</span>
                                                        <div class="form-check ml-2">
                                                            <input 
                                                            name="checkbox" 
                                                            class="form-check-input" 
                                                            type="checkbox"
                                                            value="{{ $key }}"
                                                        >
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info"><i class="fa-light fa-cart-shopping"></i></span>

                                    <div class="info-box-content">
                                        <p class="description pb-0 mb-0">Generate and export purchase report by statuses:
                                        </p>
                                        <div class="btn-group">
                                            <button type="button" data-type_export="2" data-export="xls"
                                                class="btn btn-sm btn-secondarybtn btn-sm btn-success">Export XLS</button>
                                            <button type="button" data-type_export="2" data-export="csv"
                                                class="btn btn-sm btn-primary">Export CSV</button>
                                            <button type="button" data-type_export="2" data-export="pdf"
                                                class="btn btn-sm btn-danger">Export PDF</button>
                                        </div>
                                        <p class="description pb-0 mb-0">Generate for:</p>
                                        <div class="d-flex">
                                            <div class="form-check col-3">
                                                <input 
                                                    class="form-check-input" 
                                                    type="checkbox" 
                                                    value="{{$thisMonthStart}}-{{$thisMonthEnd}}"
                                                    id="purchases_this_month"
                                                >
                                                <label class="form-check-label" for="purchases_this_month">
                                                    This month
                                                </label>
                                            </div>
                                            <div class="form-check col-3">
                                                <input 
                                                    class="form-check-input" 
                                                    type="checkbox" 
                                                    value="{{$lastMonthStart}}-{{$lastMonthEnd}}"
                                                    id="purchases_last_month"
                                                >
                                                <label class="form-check-label" for="purchases_last_month">
                                                    Last month
                                                </label>
                                            </div>
                                        </div>
                                        <div>
                                            <ul class="list-group mt-2 flex-row">
                                                <li class="d-flex list-group-item">
                                                    All
                                                    <div class="form-check ml-2">
                                                        <input name="checkbox" class="form-check-input" type="checkbox">
                                                    </div>
                                                </li>
                                                @foreach (config('statuses.payment_statuses') as $key => $status)
                                                    <li class="d-flex list-group-item">
                                                        <span>{{ $status }}</span>
                                                        <div class="form-check ml-2">
                                                            <input name="checkbox" class="form-check-input"
                                                                type="checkbox">
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info"><i class="fa-light fa-truck"></i></span>

                                    <div class="info-box-content">
                                        <p class="description pb-0 mb-0">Generate and export driver information for orders:
                                        </p>
                                        <div class="btn-group">
                                            <button type="button" data-type_export="3" data-export="xls"
                                                class="btn btn-sm btn-secondarybtn btn-sm btn-success">Export XLS</button>
                                            <button type="button" data-type_export="3" data-export="csv"
                                                class="btn btn-sm btn-primary">Export CSV</button>
                                            <button type="button" data-type_export="3" data-export="pdf"
                                                class="btn btn-sm btn-danger">Export PDF</button>
                                        </div>
                                        <p class="description pb-0 mb-0">Generate for:</p>
                                        <div class="d-flex">
                                            <div class="form-check col-3">
                                                <input 
                                                    class="form-check-input" 
                                                    type="checkbox" 
                                                    value="{{$thisMonthStart}}-{{$thisMonthEnd}}"
                                                    id="drivers_export_this_month"
                                                >
                                                <label class="form-check-label" for="drivers_export_this_month">
                                                    This month
                                                </label>
                                            </div>
                                            <div class="form-check col-3">
                                                <input 
                                                    class="form-check-input" 
                                                    type="checkbox" 
                                                    value="{{$lastMonthStart}}-{{$lastMonthEnd}}"
                                                    id="drivers_export_last_month"
                                                >
                                                <label class="form-check-label" for="drivers_export_last_month">
                                                    Last month
                                                </label>
                                            </div>
                                        </div>
                                        <div>
                                            <ul class="list-group mt-2 flex-row">
                                                <li class="d-flex justify-content-between list-group-item">
                                                    All
                                                    <div class="form-check ml-2">
                                                        <input name="checkbox" class="form-check-input" type="checkbox">
                                                    </div>
                                                </li>
                                                @foreach (config('statuses.payment_statuses') as $key => $status)
                                                    <li class="d-flex justify-content-between list-group-item">
                                                        {{ $status }}
                                                        <div class="form-check ml-2">
                                                            <input name="checkbox" class="form-check-input"
                                                                type="checkbox">
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info"><i class="fa-light fa-boxes-packing"></i></span>

                                    <div class="info-box-content">
                                        <p class="description pb-0 mb-0">Generate and export package report</p>
                                        <div class="btn-group">
                                            <button type="button" data-type_export="4" data-export="xls"
                                                class="btn btn-sm btn-secondarybtn btn-sm btn-success">Export XLS</button>
                                            <button type="button" data-type_export="4" data-export="csv"
                                                class="btn btn-sm btn-primary">Export CSV</button>
                                            <button type="button" data-type_export="4" data-export="pdf"
                                                class="btn btn-sm btn-danger">Export PDF</button>
                                        </div>
                                        <p class="description pb-0 mb-0">Generate for:</p>
                                        <div class="d-flex">
                                            <div class="form-check col-3">
                                                <input 
                                                    class="form-check-input" 
                                                    type="checkbox" 
                                                    value="{{$thisMonthStart}}-{{$thisMonthEnd}}"
                                                    id="package_export_this_month"
                                                >
                                                <label class="form-check-label" for="package_export_lthismonth">
                                                    This month
                                                </label>
                                            </div>
                                            <div class="form-check col-3">
                                                <input 
                                                    class="form-check-input" 
                                                    type="checkbox" 
                                                    value="{{$lastMonthStart}}-{{$lastMonthEnd}}"
                                                    id="package_export_last_month"
                                                >
                                                <label class="form-check-label" for="package_export_last_month">
                                                    Last month
                                                </label>
                                            </div>
                                        </div>
                                        <div>
                                            <ul class="list-group mt-2 flex-row">
                                                <li class="d-flex justify-content-between list-group-item">
                                                    All
                                                    <div class="form-check ml-2">
                                                        <input name="checkbox" class="form-check-input" type="checkbox">
                                                    </div>
                                                </li>
                                                <li class="d-flex justify-content-between list-group-item">
                                                    Delivered
                                                    <div class="form-check ml-2">
                                                        <input name="checkbox" class="form-check-input" type="checkbox">
                                                    </div>
                                                </li>
                                                <li class="d-flex justify-content-between list-group-item">
                                                    Not delivered
                                                    <div class="form-check ml-2">
                                                        <input name="checkbox" class="form-check-input" type="checkbox">
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@endpush
