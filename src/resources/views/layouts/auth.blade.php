<div class="container authentication">
    <div class='col-lg-6 col-xl-6 col-md-12 col-sm-12 m-auto'>
        <div class="card card-tabs">
            <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" 
                           id="custom-tabs-one-home-tab" 
                           data-toggle="pill" 
                           href="#custom-tabs-one-home" 
                           role="tab" 
                           aria-controls="custom-tabs-one-home" 
                           aria-selected="true"
                           >
                            Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a 
                            class="nav-link" 
                            id="custom-tabs-one-profile-tab" 
                            data-toggle="pill" 
                            href="#custom-tabs-one-profile" 
                            role="tab" 
                            aria-controls="custom-tabs-one-profile" 
                            aria-selected="false"
                            >
                            Register
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane fade active show" id="custom-tabs-one-home" role="tabpanel" aria-labelledby="custom-tabs-one-home-tab">

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
                                        class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" 
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
                                        class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" 
                                        id="password" 
                                        name='password'
                                        placeholder='Enter password'
                                        />
                                    @if (Session::has('danger'))
                                    <span class="text-danger invalid-feedback d-block">{!! Session::get('danger') !!}</span>
                                    @endif
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
                                    <button type='submit' class='btn btn-outline-dark'>
                                        Login
                                    </button>
                                </div>

                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="custom-tabs-one-profile" role="tabpanel" aria-labelledby="custom-tabs-one-profile-tab">
                        <form method='post' action="{{route('post.register')}}" >
                            @csrf
                            <div class='card-body'>
                                <div class="form-group">
                                    <label for="email">Email address</label>
                                    <input 
                                        type="email" 
                                        class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" 
                                        id="email" name='email' 
                                        placeholder='Enter valid e-mail'
                                        />
                                    @include("layouts.validation_messages",["input"=>"email"])
                                </div>
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input 
                                        type="username" 
                                        class="form-control {{ $errors->has('username') ? ' is-invalid' : '' }}" 
                                        id="username" 
                                        name='username'
                                        placeholder='Enter username'
                                        />
                                    @include("layouts.validation_messages",["input"=>"username"])
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input 
                                        type="password" 
                                        class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" 
                                        id="password" 
                                        name='password'
                                        placeholder='Enter password'
                                        />
                                    @include("layouts.validation_messages",["input"=>"password"])
                                </div>
                                <div class="form-group">
                                    <label for="password">Confirm password</label>
                                    <input 
                                        type="password" 
                                        class="form-control {{ $errors->has('confirm-password') ? ' is-invalid' : '' }}" 
                                        id="confirm-password" 
                                        name='confirm-password' 
                                        placeholder='Confirm password'
                                        />
                                </div>

                                <div class="form-group">
                                    <button type='submit' class='btn btn-outline-dark'>
                                        Register
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>