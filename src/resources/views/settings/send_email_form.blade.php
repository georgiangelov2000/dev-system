@extends('app')
@section('title', 'Company settings')

@section('content')
    <div class="card card-default cardTemplate">
        <div class="card-header">
            <div class="col-12">
                <h3 class="card-title">Send email settings</h3>
            </div>
        </div>
        <div class="card-body">

            <div class="col-12">

                <form method="POST" action="{{route('settings.email.send')}}">
                    @csrf
                    <div class="col-6 d-flex flex-wrap">
                        <div class="form-group col-12">
                            <label for="email">Company email</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Enter e-mail"
                                value="{{ $company_email }}">
                        </div>

                        <div class="form-group col-12">
                            <label for="email">Client email</label>
                            <input type="email" id="client_email" name="client_email" placeholder="Email" class="form-control">
                        </div>

                        <div class="form-group col-12">
                            <label for="message_type">Message</label>
                            <select class="form-control messageType" name="message_type" id="message_type">
                                <option value="">Nothing selected</option>
                                <option value="Informational">Informational</option>
                                <option value="Confirmation">Confirmation</option>
                                <option value="Urgent Notice">Urgent Notice</option>
                                <option value="Announcement">Announcement</option>
                                <option value="Apology">Apology</option>
                            </select>
                        </div>

                        <div class="form-group col-12">
                            <label for="title">Title</label>
                            <input type="text" name="title" class="form-control" placeholder="Enter title">
                        </div>

                        <div class="form-group col-12">
                            <label for="content">Content</label>
                            <textarea name="content" class="form-control" id="content" cols="30" rows="3"></textarea>
                        </div>

                        <div class="form-group col-12">
                            <div style="height:30px">
                                <label for="file">File</label>
                            </div>
                            <div class="custom-file col-12">
                                <input type="file" name="file" id="file" class="custom-file-input">
                                <label class="custom-file-label" for="customFile">Choose file</label>
                            </div>
                        </div>

                        <div class="form-group col-12">
                            <button type="submit" class="btn btn-primary">Send</button>
                        </div>
                    </div>

                </form>
            </div>

        </div>
    </div>

    @push('scripts')
        <script type="text/javascript" src="{{ mix('js/settings/e-mail.js') }}"></script>
    @endpush

@endsection
