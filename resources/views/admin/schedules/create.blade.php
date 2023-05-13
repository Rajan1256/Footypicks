@extends('admin.layouts.inside', ['page' => 'leagues'])

@section('content')
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        {{(!isset($model->id)) ? 'Create a new Schedule' : 'Update Schedule'}}
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
                                    <label for="date">Date <span class="col-pink">*</span></label>
                                    <div class="form-line {{$errors->has('date') ? 'focused error' : ''}}">
                                        <input id="date" name="date" type="datetime" class="form-control" placeholder="2018-03-17T14:30:00Z"  value="{{ date('Y-m-d h:i', $model->date) }}"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="team_home_id">Team Home <span class="col-pink">*</span></label>
                                    <div class="form-line {{$errors->has('team_home_id') ? 'focused error' : ''}}">
                                        <select name="team_home_id" class="form-control" required>
                                            @foreach($teams as $team)
                                                <option value="{{$team->id}}" {{($team->id == $model->team_home_id) ? 'selected' : ''}}>
                                                    {{$team->name}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="team_away_id">Team Away <span class="col-pink">*</span></label>
                                    <div class="form-line {{$errors->has('team_away_id') ? 'focused error' : ''}}">
                                        <select name="team_away_id" class="form-control" required>
                                            @foreach($teams as $team)
                                                <option value="{{$team->id}}" {{($team->id == $model->team_away_id) ? 'selected' : ''}}>
                                                    {{$team->name}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary m-t-15 waves-effect">
                                    {{(!isset($model->id)) ? 'Create' : 'Update'}}
                                </button>
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