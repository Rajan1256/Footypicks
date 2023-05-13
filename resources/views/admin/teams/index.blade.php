@extends('admin.layouts.inside', ['page' => 'teams'])

@section('content')
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        Teams  <a href="{{route('a:teams:create')}}" class="btn btn-success">Create new Team</a>
                    </h2>
                </div>
                <div class="body">
                    <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Cover</th>
                            <th>League</th>
                            <th>Position</th>
                            <th>Points</th>
                            <th>W/D/L</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Cover</th>
                            <th>League</th>
                            <th>Position</th>
                            <th>Points</th>
                            <th>W/D/L</th>
                            <th>Action</th>
                        </tr>
                        </tfoot>
                        @foreach($collection as $model)
                                <tr>
                                    <td>{{$model->id}}</td>
                                    <td>{{$model->name}}</td>
                                    <td>
                                        <img src="{{$model->cover}}" width="50" height="50" alt="Team Cover" />
                                    </td>
                                    <td>{{$model->league->caption}}</td>
                                    <td>{{$model->position}}</td>
                                    <td>{{$model->points}}</td>
                                    <td>{{$model->wins}} / {{$model->draws}} / {{$model->losses}}</td>
                                    <td>
                                        <a href="{{route('a:teams:edit', $model->id)}}" class="btn btn-info btn-sm">Edit</a>
                                        {{--                                        <a href="{{route('a:league:finish', $model->id)}}" class="btn btn-warning btn-sm">Finish</a>--}}
                                        <a href="{{route('a:teams:delete', $model->id)}}" class="btn btn-danger btn-sm">Delete</a>
                                    </td>
                                </tr>
                        @endforeach
                    </table>
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