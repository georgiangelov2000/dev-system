@extends('app')

@section('content')
    @include('dashboard.templates.header', [
        'company_information' => $dashboard->company_information,
    ])
    <div class="row">
        @include('dashboard.templates.boxes', [
            'stats' => $dashboard->stats,
        ])
    </div>
    @include('dashboard.templates.statistics', [
        'result' => $dashboard->summary,
    ])
@endsection
