@extends('admin.layouts.main')

@section('style')
    <!-- Waves Effect Css -->
    <link href="/plugins/node-waves/waves.css" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="/plugins/animate-css/animate.css" rel="stylesheet" />

    <link href="{{ asset('css/login.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="login-box">
    <div class="logo">
        <img src="/images/logo.png" class="image-logo">
        <a href="javascript:void(0);">Admin panel</a>
        <small>{{ config('app.name', 'Laravel') }}</small>
    </div>
    <div class="card">
        <div class="body">
            <form id="sign_in" role="form" method="POST" action="{{ route('login') }}">
                {{ csrf_field() }}
                <div class="msg">Sign</div>
                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                    @if(session()->has('message'))
                        <div class="alert alert-success">
                            {{ session()->get('message') }}
                        </div>
                    @endif
                <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">person</i>
                        </span>
                    <div class="form-line {{($errors->has('email')) ? 'focused error' : ''}}">
                        <input type="text" class="form-control" name="email" placeholder="Username" required autofocus value="{{ old('email') }}">
                    </div>
                </div>
                <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                        </span>
                    <div class="form-line {{($errors->has('password')) ? 'focused error' : ''}}">
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                        @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-8 p-t-5">
                        <input type="checkbox" name="remember" id="rememberme" class="filled-in chk-col-pink">
                        <label for="rememberme">Remember Me</label>
                    </div>
                    <div class="col-xs-4">
                        <button class="btn btn-block bg-pink waves-effect" type="submit">SIGN IN</button>
                    </div>
                    <div class="col-xs-12">
                        <a href="{{ route('a:resetPassPage') }}">Reset Password</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')

    <!-- Jquery Core Js -->
    <script src="/plugins/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core Js -->
    <script src="/plugins/bootstrap/js/bootstrap.js"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="/plugins/node-waves/waves.js"></script>

    <!-- Validation Plugin Js -->
    <script src="/plugins/jquery-validation/jquery.validate.js"></script>

    <!-- Scripts -->
    <script src="{{ asset('js/login.js') }}"></script>
@endsection