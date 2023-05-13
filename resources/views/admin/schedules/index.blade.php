@extends('admin.layouts.inside', ['page' => 'schedules'])

@section('content')
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        Schedules
                    </h2>
                </div>
		                <div class="body">
                    <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>League</th>
                            <th>Team home</th>
                            <th>Team away</th>
                            <th>Match day</th>
                            <th>Status</th>
                            <th>Goals home team</th>
                            <th>Goals away team</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Id</th>
                            <th>League</th>
                            <th>Team home</th>
                            <th>Team away</th>
                            <th>Match day</th>
                            <th>Status</th>
                            <th>Goals home team</th>
                            <th>Goals away team</th>
                            <th>Action</th>
                        </tr>
                        </tfoot>
                        @foreach($collection as $model)
                                <tr>
                                    <td>{{$model->id}}</td>
                                    <td>{{$model->league->caption}}</td>
                                    <td>{{$model->teamHome->name}}</td>
                                    <td>{{$model->teamAway->name}}</td>
                                    <td>{{$model->matchday}}</td>
                                    <td>{{$model->status}}</td>
                                    <td>{{$model->goals_home_team}}</td>
                                    <td>{{$model->goals_away_team}}</td>
                                    <td>
                                        <a href="{{route('a:schedules:edit', $model->id)}}" class="btn btn-info btn-sm">Edit</a>
                                         @if($model->status=='FINISHED')
                                                <a class="btn btn-warning btn-sm" data-toggle="modal" data-target="#myModal">Set Result</a>
                                        @else
                                            <a href="{{route('a:schedules:finish', $model->id)}}" class="btn btn-warning btn-sm">Set Result</a>
                                        @endif
                                        <a href="{{route('a:schedules:delete', $model->id)}}" class="btn btn-danger btn-sm">Delete</a>
                                    </td>
                                </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>

<div class="container">
        <div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Warning!</h4>
                    </div>
                    <div class="modal-body">
                        <p>Schedule is already finish you can't change the score.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection


@section('script')

    <!-- JQuery DataTable Css -->
    <link href="/plugins/jquery-datatable/dataTables.bootstrap.css" rel="stylesheet">

    <!-- Jquery Core Js -->
    <script src="/plugins/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core Js -->
    <script src="/plugins/bootstrap/js/bootstrap.js"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="/plugins/node-waves/waves.js"></script>

    <!-- Validation Plugin Js -->
    <script src="/plugins/jquery-validation/jquery.validate.js"></script>
    <!-- Jquery DataTable Plugin Js -->
    <script src="/plugins/jquery-datatable/jquery.dataTables.js"></script>
    <script src="/plugins/jquery-datatable/dataTables.bootstrap.js"></script>
    <script src="/plugins/jquery-datatable/dataTables.buttons.min.js"></script>
    <script src="/plugins/jquery-datatable/buttons.flash.min.js"></script>
    <script src="/plugins/jquery-datatable/jszip.min.js"></script>
    <script src="/plugins/jquery-datatable/pdfmake.min.js"></script>
    <script src="/plugins/jquery-datatable/vfs_fonts.js"></script>
    <script src="/plugins/jquery-datatable/buttons.html5.min.js"></script>
    <script src="/plugins/jquery-datatable/buttons.print.min.js"></script>

    <script>
        $(function () {
            $('.js-basic-example').DataTable();

            var exportOptions = {
                columns: [ 0, 1, 2, 3, 4]
            };
            //Exportable table
            $('.js-exportable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'print',
                        exportOptions: exportOptions
                    },
                    {
                        extend: 'copy',
                        exportOptions: exportOptions
                    },
                    {
                        extend: 'csv',
                        exportOptions: exportOptions
                    },
                    {
                        extend: 'excel',
                        exportOptions: exportOptions
                    },
                    {
                        extend: 'pdf',
                        exportOptions: exportOptions
                    }
                ],
            });
        });
    </script>
    <!-- Scripts -->
    <script src="{{ asset('js/login.js') }}"></script>
@endsection