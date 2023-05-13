@extends('admin.layouts.inside')

@section('content')
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