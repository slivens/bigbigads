@extends('layouts.app')

@section('content')
<div class="container reset-content">
    <div class="content">
        
        
            <h3 class="reset-title">Reset Password</h3>
            <p>Please reset your password within 2 hours!</p>

            <div class="panel-body">
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/reset') }}">
                    {{ csrf_field() }}

                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <div class="input-icon">
                            <i class="fa fa-envelope"></i>
                            <input id="email" type="email" class="form-control" name="email" placeholder="Email" value="{{ $email or old('email') }}" required autofocus>

                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                    </div>
                        
                    </div>

                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <div class="input-icon">
                            <i class="fa fa-lock"></i>
                            <input id="password" type="password" class="form-control placeholder-no-fix" placeholder="Password" name="password" required>

                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                        
                    </div>

                    <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                        <div class="input-icon">
                            <i class="fa fa-check"></i>
                            <input id="password-confirm" type="password" class="form-control" placeholder="Confirm Password" name="password_confirmation" required>

                            @if ($errors->has('password_confirmation'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                </span>
                            @endif
                        </div>                   
                    </div>

                    <div class="form-group button-div margin-top-20">
                        
                        <button type="submit" class="btn margin-top-30">
                            Reset Password
                        </button>
                        
                    </div>
                </form>
            </div>
       
        
    </div>
</div>
@endsection
