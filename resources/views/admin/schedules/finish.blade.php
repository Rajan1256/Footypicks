@extends('admin.layouts.inside', ['page' => 'leagues'])

@section('content')
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        Set results to schedule {{$model->teamHome->name}} - {{$model->teamAway->name}}
                    </h2>
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
                                    <label for="goals_home_team">Goals home team <span class="col-pink">*</span> ({{$model->teamHome->name}})</label>
                                    <div class="form-line {{$errors->has('goals_home_team') ? 'focused error' : ''}}">
                                        <input name="goals_home_team" type="number" min="0" class="form-control" placeholder="Goals home team"  value="{{ $model->goals_home_team }}"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="goals_away_team">Goals away team <span class="col-pink">*</span> ({{$model->teamAway->name}})</label>
                                    <div class="form-line {{$errors->has('goals_away_team') ? 'focused error' : ''}}">
                                        <input name="goals_away_team" type="number" min="0" class="form-control" placeholder="Goals away team"  value="{{ $model->goals_away_team }}"/>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary m-t-15 waves-effect">Save</button>
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