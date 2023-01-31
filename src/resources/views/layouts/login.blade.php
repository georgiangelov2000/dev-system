@extends('app')

@section('content')
<div class='col-lg-5 col-xl-5 col-md-12 col-sm-12 m-auto'>
    <div class='card'>
        <div class='card-header'>
            <h3>Login form</h3>
        </div>
        <form 
            method='post' 
            action="{{route('post.login')}}"
            >
            @csrf
            <div class='card-body'>
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input 
                        type="email" 
                        class="form-control" 
                        id="email" 
                        name='email'
                        placeholder='Enter valid e-mail'
                        />
                    @include("layouts.validation_messages",["input"=>"email"])
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input 
                        type="password" 
                        class="form-control" 
                        id="password" 
                        name='password'
                        placeholder='Enter password'
                        />
                    @include("layouts.validation_messages",["input"=>"password"])
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input 
                            class="form-check-input" 
                            type="checkbox" 
                            name="remember" 
                            id="remember" {{ old('remember') ? 'checked' : '' }}>

                        <label class="form-check-label" for="remember">
                            {{ __('Remember Me') }}
                        </label>
                    </div>
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