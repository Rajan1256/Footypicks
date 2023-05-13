@extends('admin.layouts.inside', ['page' => 'users'])

@section('content')
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        Users
                    </h2>
                </div>
                <div class="body">
                    <table id="usersData" class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>Email</th>
                            <th>Name</th>
                            <th>Nickname</th>
                            <th>Created at</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Id</th>
                            <th>Email</th>
                            <th>Name</th>
                            <th>Nickname</th>
                            <th>Created at</th>
                            <th>Action</th>
                        </tr>
                        </tfoot>
                        @foreach($collection as $player)

                            
                            <tr>
                                <td>{{$player->id}}</td>
                                <td>{{$player->email}}</td>
                                <td>{{$player->name}}</td>
                                <td>{{$player->nickname}}</td>
                                <td>{{date('Y-m-d', $player->created_at)}}</td>
                                <td><a class="btn btn-danger" href="javascript:void(0)" onclick="deleteUers('{{$player->id}}');"><span>Delete</span></a></td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')

    <!-- For SweetAlert2 -->
    <script src="{{ url('/') }}/sweetalert.js"></script>

    <!-- JQuery DataTable Css -->
    <link href="{{ url('/') }}/plugins/jquery-datatable/dataTables.bootstrap.css" rel="stylesheet">

    <!-- Jquery Core Js -->
    <script src="{{ url('/') }}/plugins/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core Js -->
    <script src="{{ url('/') }}/plugins/bootstrap/js/bootstrap.js"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="{{ url('/') }}/plugins/node-waves/waves.js"></script>

    <!-- Validation Plugin Js -->
    <script src="{{ url('/') }}/plugins/jquery-validation/jquery.validate.js"></script>
    <!-- Jquery DataTable Plugin Js -->
    <script src="{{ url('/') }}/plugins/jquery-datatable/jquery.dataTables.js"></script>
    <script src="{{ url('/') }}/plugins/jquery-datatable/dataTables.bootstrap.js"></script>
    <script src="{{ url('/') }}/plugins/jquery-datatable/dataTables.buttons.min.js"></script>
    <script src="{{ url('/') }}/plugins/jquery-datatable/buttons.flash.min.js"></script>
    <script src="{{ url('/') }}/plugins/jquery-datatable/jszip.min.js"></script>
    <script src="{{ url('/') }}/plugins/jquery-datatable/pdfmake.min.js"></script>
    <script src="{{ url('/') }}/plugins/jquery-datatable/vfs_fonts.js"></script>
    <script src="{{ url('/') }}/plugins/jquery-datatable/buttons.html5.min.js"></script>
    <script src="{{ url('/') }}/plugins/jquery-datatable/buttons.print.min.js"></script>

    <script>
        function deleteUers(Id)
        {
            console.log(Id);
            
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            swal({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                if (result.value) {
                    $.ajax({
//                        url: "{{ route('delete') }}",
                        url: "{{ route('deleteUsers') }}",
                        type: 'POST',
                        data: {_token: CSRF_TOKEN, message:Id},
                        dataType: 'JSON',
                        success: function(data) {
                            console.log(data);
                        }
                    });
                    swal({title: "Deleted", text: "Your file has been deleted.", type: "success"}).then(function(){ 
                           location.reload(true);
                        }
                    );
                }
            });
            
        }
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