@extends('app')
@section('title', 'Create company payments')

@section('content')
    <div class="row justify-content-between mb-3">
        <div class="col-12 d-flex justify-content-between">
            <h3 class="mb-0">Create company payments</h3>
        </div>
    </div>
    <div class="row">
        <div class="card col-12 cardTemplate">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <p class="bg-dark p-2 font-weight-bold filters">
                            <i class="fa-solid fa-filter"></i> Filters
                        </p>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Suppliers</label>
                            <select name="supplier_id" class="form-control selectSupplier" data-live-search="true">
                            </select>
                        </div>
                    </div>
                    <div class="col-3">
                        <label for="customRange1">Created</label>
                        <input type="text" class="form-control pull-right" name="datetimes" />
                    </div>
                </div>
                <form class="col-12" action="">
                    <table id="purchases" class="table table-hover table-sm">
                        <thead>
                            <th>
                                <div class="form-check">
                                    <input class="form-check-input selectAll" type="checkbox">
                                    <label class="form-check-label" for="flexCheckDefault"></label>
                                </div>
                            </th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Total price</th>
                            <th>Quantity</th>
                            <th>Init.quantity</th>
                            <th>Paid quantity</th>
                            <th>Paid total price</th>
                            <th>Date of payment</th>
                            <th>Code</th>
                            <th>Created</th>
                            <th>Paid</th>
                        </thead>
                    </table>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script type="text/javascript" src="{{ mix('js/purchases/payments.js') }}"></script>
        <script type="text/javascript">
            const PRODUCT_API_ROUTE = "{{ route('api.products') }}";
            const SUPPLIER_API_ROUTE = "{{ route('api.suppliers') }}";
        </script>
    @endpush

@endsection
