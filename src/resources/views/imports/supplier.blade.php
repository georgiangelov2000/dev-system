@extends('app')

@section('content')
    @include('templates.import_form', [
        'title' => 'Import suppliers',
        'thRows' => [
            'Name' => '(Required string)',
            'Email' => '(Required email)',
            'Phone' => '(Required string)',
            'Address' => '(Required string)',
            'Website' => '(Required string)',
            'Zip code' => '(Required number)',
            'Country' => '(Required number)',
            'State' => '(Required string)',
            'Categories' => '(Format 1&2)',
        ],
        'tbodyRows' => [
            'John doe',
            'johndoe@gmail.com',
            '44 7911 123456',
            '39 Henry Smith Avenue Indio, CA 92201',
            'https://example.com',
            '95076',
            '(Country ID)',
            '(State ID)',
            '(Category ID)',
        ],
        'type' => 'supplier',
    ])
    @push('scripts')
        <script type="text/javascript" src="{{ mix('js/csv/import.js') }}"></script>
    @endpush
@endsection
