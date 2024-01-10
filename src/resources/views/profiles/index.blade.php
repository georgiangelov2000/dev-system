@extends('app')

@section('content')
    <div class="container">
        <div class="col-md-12">

            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        @if ($user->photo)
                            <img class="profile-user-img img-fluid img-circle" src="{{ $user->photo }}" alt="Profile Picture">
                        @else
                            <img class="img-fluid rounded-circle w-25" src="https://via.placeholder.com/150"
                                alt="Profile Placeholder">
                        @endif
                    </div>
                    <h3 class="profile-username text-center">{{ $user->username }}</h3>
                    <p class="text-muted text-center">{{ $user->role->name }}</p>
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Email</b> <a class="float-right">{{ $user->email }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Username</b> <a class="float-right">{{ $user->username }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Address</b> <a class="float-right">{{ $user->address }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Gender</b> <a class="float-right">{{ $genders[$user->gender] }}</a>
                        </li>
                        <li class="list-group-item">
                            @php
                                // Extract year, month, and day from the original date
                                [$year, $month, $day] = explode('-', $user->birth_date);

                                // Convert month number to month name
                                $monthName = date('F', mktime(0, 0, 0, $month, 1));

                                // Create the formatted date string
                                $formattedDate = $monthName . ' ' . $day . ', ' . $year;
                            @endphp
                            <b>Birth date</b> <a class="float-right">{{ $formattedDate }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Permissions</b> 
                            @if(count($user->role->rolesAccessManagement))
                                <div class="float right d-flex flex-wrap">
                                    @foreach ($user->role->rolesAccessManagement as $item)
                                    <h6 class="mr-1">
                                        <span class="badge badge-primary">
                                            {{ $item->access }}
                                        </span>
                                    </h6>
                                    @endforeach
                                </div>
                            @endif
                        </li>
                    </ul>
                </div>

            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $('select[name="gender"]').selectpicker();
        });
    </script>
@endpush
