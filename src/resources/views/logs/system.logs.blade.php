@extends('app')

@section('content')
    <div class="row">
        <div class="card col-12 cardTemplate">
            <div class="card-header bg-primary">
                <div class="col-12">
                    <h3 class="card-title">System Logs</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="row table-responsive">
                    <table id="logsTable" class="table table-hover table-sm dataTable no-footer">
                        <thead>
                            <tr>
                                <th>IP</th>
                                <th>Method</th>
                                <th>URL</th>
                                <th>User Agent</th>
                                <th>Headers</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
      
    @push('scripts')
        <script type="text/javascript" src="{{ mix('js/logs/logs.js') }}"></script>
        <script>
            const LOG_API_ROUTE = "{{ route('api.logs') }}"
        </script>
    @endpush
@endsection
