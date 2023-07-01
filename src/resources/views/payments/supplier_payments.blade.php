@extends('app')
@section('title', 'Purchase payments')

@section('content')
    <div class="row">
        <div class="card col-12 cardTemplate">
            <div class="card-body">
                <div class="form-row">
                    <div class="col-3">
                        <label for="customRange1">Select supplier</label>
                        <select class="form-control selectSupplier" name="customer">
                            <option value="0">Nothing selected</option>
                            @foreach ($suppliers as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
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
                        <button title="Filter" class="btn btn-primary filter" type="button">
                            <i class="fa-light fa-magnifying-glass"></i>
                        </button>
                    </div>
                </div>
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
        <div class="col-12 p-0">
            <div id="paymentTemplate" class="col-12 table-responsive">

            </div>
        </div>
    </div>

    @push('scripts')
        <script type="text/javascript" src="{{ mix('js/payments/supplier_payments.js') }}"></script>
        <script type="text/javascript">
            const SUPPLIER_PAYMENTS_API = "{{ route('api.supplier.payments') }}";
            const SUPPLIER_PAYMENT_EDIT = "{{ route('payment.supplier.edit',':id') }}"
        </script>
    @endpush

@endsection
