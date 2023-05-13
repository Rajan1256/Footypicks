@extends('admin.layouts.inside', ['page' => 'leagues'])

@section('content')
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        {{(!isset($model->id)) ? 'Create a new Team' : 'Update Team' . $model->caption}}
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
                                    <label for="name">Caption <span class="col-pink">*</span></label>
                                    <div class="form-line {{$errors->has('name') ? 'focused error' : ''}}">
                                        <input name="name" type="text" class="form-control" placeholder="Manchester United FC"  value="{{ $model->name }}"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="league_id">League <span class="col-pink">*</span></label>
                                    <div class="form-line {{$errors->has('name') ? 'focused error' : ''}}">
                                        <select name="league_id" class="form-control" required>
                                            @foreach($leagues as $league)
                                                <option value="{{$league->id}}" {{($league->id == $model->league_id) ? 'selected' : ''}}>
                                                    {{$league->caption}}
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