@extends('app')

@section('content')

    @include('templates.import_form', [
        "title" => "Import brands",
        "type" => "brand",
        "thRows" => [
            "Name" => "(Required string)",
            "Description" => "(Optional description)"
        ],
        "tbodyRows" => [
            "Apple",
            "Lorem Ipsum is simply dummy text of the printing and typesetting industry."
        ],
    ])

    @push('scripts')
        <script type="text/javascript" src="{{ mix('js/csv/import.js') }}"></script>
    @endpush
@endsection
