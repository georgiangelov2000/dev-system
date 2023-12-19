@extends('app')

@section('content')
    <div class="row flex-wrap">

        <div class="card card-default cardTemplate col-12 mr-1">
            <div class="card-header bg-primary">
                <div class="col-12">
                    <h3 class="card-title">Package operations</h3>
                </div>
            </div>
            <div class="card-body">

                    <div class="row flex-wrap">
                        <div class="form-group col-3 p-0">
                            <label for="package_id">Packages</label>
                            <select class="form-control" name="package_id" id="package_id">
                                <option value="">Please select</option>
                                @foreach ($packages as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->package_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <form id="saveOrders" method="PUT" action="{{route('packages.update.form.operations')}}">
                        @csrf
                        <div class="row table-responsive">
                        <table id="formOperations" class="table  table-hover table-sm dataTable no-footer">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="form-check">
                                            <input class="form-check-input selectAll" type="checkbox">
                                            <label class="form-check-label" for="flexCheckDefault"></label>
                                        </div>
                                    </th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Tracking number</th>
                                    <th>Payment method</th>
                                    <th>Delivered</th>
                                    <th>Date of payment</th>
                                    <th>Delivery date</th>
                                    <th>Invoice number</th>
                                    <th>Invoice date</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        </div>
                    </form>
                    <div id="submitWrapper"></div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script type="text/javascript" src="{{mix('js/packages/form_operations.js')}}"></script>
    <script type="text/javascript">
        const ORDERS_API_ROUTE = "{{ route('api.orders') }}";
    </script>
    @endpush
@endsection
