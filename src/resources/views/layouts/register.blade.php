@extends('app')

@section('content')
<div class='col-lg-5 col-xl-5 col-md-12 col-sm-12 m-auto'>
    <div class='card'>
        <div class='card-header'>
            <h3>Register form</h3>
        </div>
        <form>
            <div class='card-body'>
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="email" class="form-control" id="email" name='email' />
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="username" class="form-control" id="username" name='username' />
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name='password' />
                </div>
                <div class="form-group">
                    <label for="password">Confirm password</label>
                    <input type="password" class="form-control" id="confirm-password" name='confirm-password' />
                </div>

                <div class="form-group">
                    <button type='submit' class='btn btn-outline-primary'>
                        Register
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection