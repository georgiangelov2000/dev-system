@extends('app')

@section('content')
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
                                            <button type="button"
                                                class="btn btn-sm btn-secondarybtn btn-sm btn-success">Export XLS</button>
                                            <button type="button" class="btn btn-sm btn-primary">Export CSV</button>
                                            <button type="button" class="btn btn-sm btn-danger">Export PDF</button>
                                        </div>
                                        <p class="description pb-0 mb-0">Generate for:</p>
                                        <div class="d-flex">
                                            <div class="form-check col-3">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="defaultCheck1">
                                                <label class="form-check-label" for="defaultCheck1">
                                                    This month
                                                </label>
                                            </div>
                                            <div class="form-check col-3">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="defaultCheck2">
                                                <label class="form-check-label" for="defaultCheck2">
                                                    Last month
                                                </label>
                                            </div>
                                        </div>
                                        <div>
                                            <ul class="list-group mt-2">
                                                @foreach (config('statuses.payment_statuses') as $key => $status)
                                                    <li class="d-flex justify-content-between list-group-item">
                                                        {{ $status }}
                                                        <div class="form-check">
                                                            <input 
                                                                name="checkbox" 
                                                                class="form-check-input" 
                                                                type="checkbox"
                                                                value="orders.{{$key}}.{{trim(strtolower($status))}}"
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
                                        <p class="description pb-0 mb-0">Generate and export purchase report by statuses:</p>
                                        <div class="btn-group">
                                            <button type="button"
                                                class="btn btn-sm btn-secondarybtn btn-sm btn-success">Export XLS</button>
                                            <button type="button" class="btn btn-sm btn-primary">Export CSV</button>
                                            <button type="button" class="btn btn-sm btn-danger">Export PDF</button>
                                        </div>
                                        <p class="description pb-0 mb-0">Generate for:</p>
                                        <div class="d-flex">
                                            <div class="form-check col-3">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="defaultCheck1">
                                                <label class="form-check-label" for="defaultCheck1">
                                                    This month
                                                </label>
                                            </div>
                                            <div class="form-check col-3">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="defaultCheck2">
                                                <label class="form-check-label" for="defaultCheck2">
                                                    Last month
                                                </label>
                                            </div>
                                        </div>
                                        <div>
                                            <ul class="list-group mt-2">
                                                @foreach (config('statuses.payment_statuses') as $key => $status)
                                                    <li class="d-flex justify-content-between list-group-item">
                                                        {{ $status }}
                                                        <div class="form-check">
                                                            <input name="checkbox" class="form-check-input" type="checkbox">
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
                                        <p class="description pb-0 mb-0">Generate and export purchase report by statuses:</p>
                                        <div class="btn-group">
                                            <button type="button"
                                                class="btn btn-sm btn-secondarybtn btn-sm btn-success">Export XLS</button>
                                            <button type="button" class="btn btn-sm btn-primary">Export CSV</button>
                                            <button type="button" class="btn btn-sm btn-danger">Export PDF</button>
                                        </div>
                                        <p class="description pb-0 mb-0">Generate for:</p>
                                        <div class="d-flex">
                                            <div class="form-check col-3">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="defaultCheck1">
                                                <label class="form-check-label" for="defaultCheck1">
                                                    This month
                                                </label>
                                            </div>
                                            <div class="form-check col-3">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="defaultCheck2">
                                                <label class="form-check-label" for="defaultCheck2">
                                                    Last month
                                                </label>
                                            </div>
                                        </div>
                                        <div>
                                            <ul class="list-group mt-2">
                                                @foreach (config('statuses.payment_statuses') as $key => $status)
                                                    <li class="d-flex justify-content-between list-group-item">
                                                        {{ $status }}
                                                        <div class="form-check">
                                                            <input name="checkbox" class="form-check-input" type="checkbox">
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
                                        <p class="description pb-0 mb-0">Generate and export purchase report by statuses:</p>
                                        <div class="btn-group">
                                            <button type="button"
                                                class="btn btn-sm btn-secondarybtn btn-sm btn-success">Export XLS</button>
                                            <button type="button" class="btn btn-sm btn-primary">Export CSV</button>
                                            <button type="button" class="btn btn-sm btn-danger">Export PDF</button>
                                        </div>
                                        <p class="description pb-0 mb-0">Generate for:</p>
                                        <div class="d-flex">
                                            <div class="form-check col-3">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="defaultCheck1">
                                                <label class="form-check-label" for="defaultCheck1">
                                                    This month
                                                </label>
                                            </div>
                                            <div class="form-check col-3">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="defaultCheck2">
                                                <label class="form-check-label" for="defaultCheck2">
                                                    Last month
                                                </label>
                                            </div>
                                        </div>
                                        <div>
                                            <ul class="list-group mt-2">
                                                @foreach (config('statuses.payment_statuses') as $key => $status)
                                                    <li class="d-flex justify-content-between list-group-item">
                                                        {{ $status }}
                                                        <div class="form-check">
                                                            <input name="checkbox" class="form-check-input" type="checkbox">
                                                        </div>
                                                    </li>
                                                @endforeach
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
