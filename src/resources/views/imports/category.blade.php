@extends('app')

@section('content')
    @include('templates.import_form', [
        'title' => 'Import suppliers',
        'thRows' => [
            'Name' => '(Required string)',
            'Description' => '(Optional string)',
            'Subcategories' => '(Subcategory Ids)',
        ],
        'tbodyRows' => [
            'Test',
            'Test description',
            '1&2'
        ],
        'type' => 'category',
    ])
    @push('scripts')
        <script type="text/javascript" src="{{ mix('js/csv/import.js') }}"></script>
    @endpush
@endsection
