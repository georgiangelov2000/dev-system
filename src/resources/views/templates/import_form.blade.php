<form action="{{ route('import.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="card card-default cardTemplate">
        <div class="card-header">
            <div class="col-12">
                <h3 class="card-title">{{ $title }}</h3>
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
                </div>
                <table class="table table-hover table-sm">
                    <thead>
                        <tr>
                            @if (count($thRows))
                                @foreach ($thRows as $key => $item)
                                    <th>{{ $key }} <b>{{ $item }}</b> </th>
                                @endforeach
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            @if (count($tbodyRows))
                                @foreach ($tbodyRows as $item)
                                    <th>{{ $item }}</th>
                                @endforeach
                            @endif
                        </tr>
                    </tbody>
                </table>
                <div class="row flex-wrap">
                    <div class="form-group col-12">
                        <div style="height:30px">
                            <label for="image">File</label>
                        </div>
                        <input type="hidden" name="type" value="{{ $type }}">
                        <div class="custom-file col-12">
                            <input id="fileInput" type="file" class="custom-file-input" accept=".csv" name="file"
                                id="file" />
                            <label class="custom-file-label" for="customFile">Choose file</label>
                            @error('file')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div id="tableContainer" class="col-lg-12 col-xl-12 col-md-12 col-sm-12"></div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</form>
