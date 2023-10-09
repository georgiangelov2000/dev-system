@extends('app')

@section('content')

    <div class="row">
        <div class="card col-12 cardTemplate">

            <div class="card-header d-flex align-items-center p-2">
                <div class="col-10">
                    <h3 class="card-title">Countries</h3>
                </div>
            </div>

            <div class="card-body">
                <table id="countries" class="table table-hover table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Flag</th>
                            <th>Name</th>
                            <th>Customers</th>
                            <th>Suppliers</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script type="text/javascript" src="{{mix('js/countries/countries.js')}}"></script>
        <script type="text/javascript">
            const API_COUNTRY_ROUTE = "{{route('api.countries')}}";
            const API_STATES  = "{{ route('api.location')}}"
        </script>
    @endpush

@endsection
