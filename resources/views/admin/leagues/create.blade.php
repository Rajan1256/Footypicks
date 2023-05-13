@extends('admin.layouts.inside', ['page' => 'leagues'])

@section('content')
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        Create a new League
                    </h2>
                    <hr>
                    @if($errors->count())
                        @foreach ($errors->all() as $errorOne)
                            <div class="alert bg-pink alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                <div>{{ $errorOne }}</div>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="body">
                    <form method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row clearfix">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="caption">Caption <span class="col-pink">*</span></label>
                                    <div class="form-line {{$errors->has('caption') ? 'focused error' : ''}}">
                                        <input name="caption" type="text" class="form-control" placeholder="Premier League"  value="{{ old('caption') }}"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="name">Name <span class="col-pink">*</span></label>
                                    <div class="form-line {{$errors->has('name') ? 'focused error' : ''}}">
                                        <input name="name" type="text" class="form-control" placeholder="PL"  value="{{ old('name') }}"/>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary m-t-15 waves-effect">Create</button>
                            </div>
                        </div>
                    </form>
                </div>
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
    <!-- Scripts -->
    <script src="{{ asset('js/login.js') }}"></script>
@endsection