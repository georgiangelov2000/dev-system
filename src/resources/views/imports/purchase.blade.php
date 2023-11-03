@extends('app')

@section('content')

    @include('templates.import_form', [
        "title" => "Import Purchases",
        "type" => "purchase",
        "thRows" => [
            "name" => "(Required)",
            "code" => "(Required)",
            "supplier_id" => "(Required)",
            "category_id" => "(Required)",
            "subcategories" => "(Optional)",
            "notes" => "(Optional)",
            "brands" => "(Optional)",
            "delivery date" => "(Required)",
            "expected date of payment" => "(Required)",
            "discount_percent" => "(Optional)",
            "price" => "(Required)",
            "quantity" => "(Required)"
            
        ],
        "tbodyRows" => [
            "Iphone5",
            "QN89Hv37IT5cVBFWcWYW",
            "2",
            "2",
            "5 or (5&1) for array",
            "Simple description",
            "2023-10-14",
            "2023-10-15",
            "1",
            "20.50",
            "2"
        ],
    ])

    @push('scripts')
        <script type="text/javascript" src="{{ mix('js/csv/import.js') }}"></script>
    @endpush
@endsection