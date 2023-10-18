@extends('app')

@section('content')
<form action="{{ route('import.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="card card-default cardTemplate">
        <div class="card-header">
            <div class="col-12">
                <h3 class="card-title">Import purchases</h3>
            </div>
        </div>
        <div class="card-body">
            <div class="col-12">
                <div class="row flex-wrap">
                    <div class="p-0 col-lg-12 col-xl-12 col-md-12 col-sm-12 d-flex justify-content-between">
                        <div class="alert alert-warning col-lg-3 col-xl-3 col-md-3 col-sm-3" role="alert">
                            <b>Please set the correct columns in your file for uplading!</b>
                        </div>
                        <div class="alert alert-primary col-lg-3 col-xl-3 col-md-3 col-sm-3" role="alert">
                            <b>Files(CSV/EXCEL)</b>
                        </div>
                    </div>
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Name <b>(Required string)</b></th>
                                <th>Email  <b>(Required email)</b></th>
                                <th>Phone <b>(Required string)</b></th>
                                <th>Address <b>(Required string)</b></th>
                                <th>Website <b>(Not required string)</b></th>
                                <th>Zip code <b>(Required number)</b></th>
                                <th>Country <b>(Required number)</b></th>
                                <th>State <b>(Required string)</b></th>
                                <th>Categories <b>(Format 1&2)</b></th>
                            </tr>
                        </thead>
                        <tbody>
                        <tbody>
                            <tr>
                                <td>John doe</td>
                                <td>johndoe@gmail.com</td>
                                <td>44 7911 123456</td>
                                <td>39 Henry Smith Avenue Indio, CA 92201</td>
                                <td>https://example.com</td>
                                <td>95076</td>
                                <td>33 (ID of the country)</td>
                                <td>175 (ID of the state)</td>
                                <td>1 (ID of the category)</td>
                            </tr>
                        </tbody>
                        </tbody>
                    </table>
                </div>
                <div class="row flex-wrap">
                    <div class="form-group col-12">
                        <div style="height:30px">
                            <label for="image">File</label>
                        </div>
                        <input type="hidden" name="type" value="supplier">
                        <div class="custom-file col-12">
                            <input id="fileInput" type="file" class="custom-file-input" accept=".csv" name="file" id="file">
                            <label class="custom-file-label" for="customFile">Choose file</label>
                            @error('file')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div id="tableContainer" class="col-lg-12 col-xl-12 col-md-12 col-sm-12">

                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
    </div>
</form>

@push('scripts')
    <script type="text/javascript" src="{{ mix('js/csv/import.js') }}"></script>
@endpush

@endsection
