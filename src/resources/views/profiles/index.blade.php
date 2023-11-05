@extends('app')

@section('content')
@php
    $user = $data['user'];
    $genders = $data['genders'];
@endphp
    <div class="container">
        <div class="card shadow-lg cardTemplate">
            <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
                <div class="col-6">
                    <h3 class="mb-0">Profile</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    @if ($user->photo)
                        <img class="img-fluid rounded-circle w-25" src="{{ $user->photo }}" alt="Profile Picture">
                    @else
                        <img class="img-fluid rounded-circle w-25" src="https://via.placeholder.com/150"
                            alt="Profile Placeholder">
                    @endif
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Email:</strong> {{ $user->email }}
                    </div>
                    <div class="col-md-6">
                        <strong>Username:</strong> {{ $user->username }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Address:</strong> {{ $user->address }}
                    </div>
                    <div class="col-md-6">
                        <strong>Gender:</strong> {{ $genders[$user->gender] }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        @php
                            // Extract year, month, and day from the original date
                            [$year, $month, $day] = explode('-', $user->birth_date);

                            // Convert month number to month name
                            $monthName = date('F', mktime(0, 0, 0, $month, 1));

                            // Create the formatted date string
                            $formattedDate = $monthName . ' ' . $day . ', ' . $year;
                        @endphp
                        <strong>Date of Birth:</strong> {{ $formattedDate }}
                    </div>
                    <div class="col-md-6">
                        <strong>Role:</strong> {{ $user->role->name }}
                    </div>
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
