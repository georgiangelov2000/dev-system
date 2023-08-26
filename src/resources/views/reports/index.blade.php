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

                                    <div class="info-box-content p-1">
                                        <p class="description pb-0 mb-0">Generate and export orders report by statuses:</p>
                                        <form method="POST" action="{{ route('report.take') }}">
                                            <div class="btn-group">
                                                <button type="submit" data-export="1" data-type_export="xlsx"
                                                    class="btn btn-sm btn-success">Export XLSX</button>
                                                <button type="submit" data-export="1" data-type_export="csv"
                                                    class="btn btn-sm btn-primary">Export CSV</button>
                                            </div>
                                            <p class="description mb-1 mt-1">Generate for: <i
                                                    class="fal fa-analytics fa-lg text-primary"></i></p>
                                            <div class="d-flex">
                                                <div class="form-check col-3">
                                                    <input name="month" class="form-check-input" type="checkbox"
                                                        value="{{ $thisMonthStart }} - {{ $thisMonthEnd }}"
                                                        id="orders_this_month" data-export="1" data-type_input="month">
                                                    <label class="form-check-label" for="orders_this_month">
                                                        This month
                                                    </label>
                                                </div>
                                                <div class="form-check col-3">
                                                    <input name="month" class="form-check-input" type="checkbox"
                                                        value="{{ $lastMonthStart }} - {{ $lastMonthEnd }}"
                                                        id="orders_last_month" data-export="1" data-type_input="month">
                                                    <label class="form-check-label" for="orders_last_month">
                                                        Last month
                                                    </label>
                                                </div>
                                            </div>
                                            <div>
                                                <ul class="list-group flex-row flex-wrap">
                                                    @foreach (config('statuses.payment_statuses') as $key => $status)
                                                        <li class="d-flex list-group-item">
                                                            <label class="mb-0 font-weight-normal"
                                                                for="status.{{ $key }}">{{ $status }}</label>
                                                            <div class="form-check ml-2">
                                                                <input name="options" class="form-check-input"
                                                                    type="checkbox" data-export="1"
                                                                    value="{{ $key }}"
                                                                    id="status.{{ $key }}">
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info"><i class="fa-light fa-cart-shopping"></i></span>

                                    <div class="info-box-content p-1">
                                        <p class="description pb-0 mb-0">Generate and export purchase report by statuses:</p>
                                        <form method="POST" action="{{ route('report.take') }}">
                                            <div class="btn-group">
                                                <button type="button" data-export="2" data-type_export="xlsx"
                                                    class="btn btn-sm btn-secondarybtn btn-sm btn-success">Export
                                                    XLSX</button>
                                                <button type="button" data-export="2" data-type_export="csv"
                                                    class="btn btn-sm btn-primary">Export CSV</button>
                                            </div>
                                            <p class="description mb-1 mt-1">Generate for: <i class="fal fa-analytics fa-lg text-primary"></i></p>
                                            <div class="d-flex">
                                                <div class="form-check col-3">
                                                    <input name="month" class="form-check-input" type="checkbox"
                                                        value="{{ $thisMonthStart }} - {{ $thisMonthEnd }}"
                                                        id="orders_this_month" data-export="2" data-type_input="month">
                                                    <label class="form-check-label" for="orders_this_month">
                                                        This month
                                                    </label>
                                                </div>
                                                <div class="form-check col-3">
                                                    <input name="month" class="form-check-input" type="checkbox"
                                                        value="{{ $lastMonthStart }} - {{ $lastMonthEnd }}"
                                                        id="orders_last_month" data-export="2" data-type_input="month">
                                                    <label class="form-check-label" for="orders_last_month">
                                                        Last month
                                                    </label>
                                                </div>
                                            </div>
                                            <div>
                                                <ul class="list-group mt-2 flex-row">
                                                    <li class="d-flex list-group-item">
                                                        <label class="mb-0 font-weight-normal"
                                                            for="status.0">Unpaid</label>
                                                        <div class="form-check ml-2">
                                                            <input name="options" class="form-check-input"
                                                                type="checkbox" data-export="2"
                                                                value="0"
                                                                id="status.0">
                                                        </div>
                                                    </li>
                                                    @foreach (config('statuses.payment_statuses') as $key => $status)
                                                        <li class="d-flex list-group-item">
                                                            <label class="mb-0 font-weight-normal"
                                                                for="status.{{ $key }}">{{ $status }}</label>
                                                            <div class="form-check ml-2">
                                                                <input name="options" class="form-check-input"
                                                                    type="checkbox" data-export="2"
                                                                    value="{{ $key }}"
                                                                    id="status.{{ $key }}">
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info"><i class="fa-light fa-truck"></i></span>

                                    <div class="info-box-content p-1">
                                        <p class="description pb-0 mb-0">Generate and export driver information for orders:
                                        </p>
                                        <form action="">
                                            <div class="btn-group">
                                                <button type="button" data-type_export="3" data-export="xls"
                                                    class="btn btn-sm btn-secondarybtn btn-sm btn-success">Export
                                                    XLS</button>
                                                <button type="button" data-type_export="3" data-export="csv"
                                                    class="btn btn-sm btn-primary">Export CSV</button>
                                            </div>
                                            <p class="description mb-1 mt-1">Generate for: <i class="fal fa-analytics fa-lg text-primary"></i></p>
                                            <div class="d-flex">
                                                <div class="form-check col-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        value="{{ $thisMonthStart }}-{{ $thisMonthEnd }}"
                                                        id="drivers_export_this_month">
                                                    <label class="form-check-label" for="drivers_export_this_month">
                                                        This month
                                                    </label>
                                                </div>
                                                <div class="form-check col-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        value="{{ $lastMonthStart }}-{{ $lastMonthEnd }}"
                                                        id="drivers_export_last_month">
                                                    <label class="form-check-label" for="drivers_export_last_month">
                                                        Last month
                                                    </label>
                                                </div>
                                            </div>
                                            <div>
                                                <ul class="list-group mt-2 flex-row">
                                                    <li class="d-flex justify-content-between list-group-item">
                                                        Orders
                                                        <div class="form-check ml-2">
                                                            <input 
                                                                name="data_sub_export" 
                                                                class="form-check-input"
                                                                type="checkbox"
                                                                data-export="3"
                                                                value="1"
                                                            />
                                                        </div>
                                                    </li>
                                                    <li class="d-flex justify-content-between list-group-item">
                                                        Profit grouped by order status
                                                        <div class="form-check ml-2">
                                                            <input 
                                                                name="data_sub_export" 
                                                                class="form-check-input"
                                                                type="checkbox"
                                                                data-export="3"
                                                                value="2"
                                                            />
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info"><i class="fa-light fa-boxes-packing"></i></span>

                                    <div class="info-box-content p-1">
                                        <p class="description pb-0 mb-0">Generate and export package report</p>
                                        <form action="">
                                            <div class="btn-group">
                                                <button type="button" data-type_export="4" data-export="xls"
                                                    class="btn btn-sm btn-secondarybtn btn-sm btn-success">Export
                                                    XLS</button>
                                                <button type="button" data-type_export="4" data-export="csv"
                                                    class="btn btn-sm btn-primary">Export CSV</button>
                                            </div>
                                            <p class="description mb-1 mt-1">Generate for: <i class="fal fa-analytics fa-lg text-primary"></i></p>
                                            <div class="d-flex">
                                                <div class="form-check col-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        value="{{ $thisMonthStart }}-{{ $thisMonthEnd }}"
                                                        id="package_export_this_month">
                                                    <label class="form-check-label" for="package_export_lthismonth">
                                                        This month
                                                    </label>
                                                </div>
                                                <div class="form-check col-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        value="{{ $lastMonthStart }}-{{ $lastMonthEnd }}"
                                                        id="package_export_last_month">
                                                    <label class="form-check-label" for="package_export_last_month">
                                                        Last month
                                                    </label>
                                                </div>
                                            </div>
                                            <div>
                                                <ul class="list-group mt-2 flex-row">
                                                    <li class="d-flex justify-content-between list-group-item">
                                                        Delivered
                                                        <div class="form-check ml-2">
                                                            <input name="checkbox" class="form-check-input"
                                                                type="checkbox">
                                                        </div>
                                                    </li>
                                                    <li class="d-flex justify-content-between list-group-item">
                                                        Not delivered
                                                        <div class="form-check ml-2">
                                                            <input name="checkbox" class="form-check-input"
                                                                type="checkbox">
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info"><i class="fa-light fa-users"></i></span>

                                    <div class="info-box-content p-1">
                                        <p class="description pb-0 mb-0">Generate and export customer report</p>
                                        <form action="">
                                            <div class="btn-group">
                                                <button type="button" data-type_export="4" data-export="xls"
                                                    class="btn btn-sm btn-secondarybtn btn-sm btn-success">Export
                                                    XLS</button>
                                                <button type="button" data-type_export="4" data-export="csv"
                                                    class="btn btn-sm btn-primary">Export CSV</button>
                                            </div>
                                            <p class="description mb-1 mt-1">Generate for: <i class="fal fa-analytics fa-lg text-primary"></i></p>
                                                <div class="d-flex">
                                                <div class="form-check col-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        value="{{ $thisMonthStart }}-{{ $thisMonthEnd }}"
                                                        id="package_export_this_month">
                                                    <label class="form-check-label" for="package_export_lthismonth">
                                                        This month
                                                    </label>
                                                </div>
                                                <div class="form-check col-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        value="{{ $lastMonthStart }}-{{ $lastMonthEnd }}"
                                                        id="package_export_last_month">
                                                    <label class="form-check-label" for="package_export_last_month">
                                                        Last month
                                                    </label>
                                                </div>
                                            </div>
                                            <div>
                                                <ul class="list-group mt-2 flex-row">
                                                    <li class="d-flex justify-content-between list-group-item">
                                                        Orders
                                                        <div class="form-check ml-2">
                                                            <input name="checkbox" class="form-check-input"
                                                                type="checkbox">
                                                        </div>
                                                    </li>
                                                    <li class="d-flex justify-content-between list-group-item">
                                                        Profit grouped by order status
                                                        <div class="form-check ml-2">
                                                            <input name="checkbox" class="form-check-input"
                                                                type="checkbox">
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info"><i class="fa-light fa-users"></i></span>

                                    <div class="info-box-content p-1">
                                        <p class="description pb-0 mb-0">Generate and export supplier report</p>
                                        <form action="">
                                            <div class="btn-group">
                                                <button type="button" data-type_export="4" data-export="xls"
                                                    class="btn btn-sm btn-secondarybtn btn-sm btn-success">Export
                                                    XLS</button>
                                                <button type="button" data-type_export="4" data-export="csv"
                                                    class="btn btn-sm btn-primary">Export CSV</button>
                                            </div>
                                            <p class="description mb-1 mt-1">Generate for: <i class="fal fa-analytics fa-lg text-primary"></i></p>
                                            <div class="d-flex">
                                                <div class="form-check col-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        value="{{ $thisMonthStart }}-{{ $thisMonthEnd }}"
                                                        id="package_export_this_month">
                                                    <label class="form-check-label" for="package_export_lthismonth">
                                                        This month
                                                    </label>
                                                </div>
                                                <div class="form-check col-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        value="{{ $lastMonthStart }}-{{ $lastMonthEnd }}"
                                                        id="package_export_last_month">
                                                    <label class="form-check-label" for="package_export_last_month">
                                                        Last month
                                                    </label>
                                                </div>
                                            </div>
                                            <div>
                                                <ul class="list-group mt-2 flex-row">
                                                    <li class="d-flex justify-content-between list-group-item">
                                                        Purchases
                                                        <div class="form-check ml-2">
                                                            <input name="checkbox" class="form-check-input"
                                                                type="checkbox">
                                                        </div>
                                                    </li>
                                                    <li class="d-flex justify-content-between list-group-item">
                                                        Profit grouped by purchase status
                                                        <div class="form-check ml-2">
                                                            <input name="checkbox" class="form-check-input"
                                                                type="checkbox">
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script type="text/javascript" src="{{ mix('js/reports/reports.js') }}"></script>
    @endpush
@endsection
