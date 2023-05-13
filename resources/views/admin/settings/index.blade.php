@extends('admin.layouts.inside', ['page' => 'users'])
<link rel="stylesheet" type="text/css" href="https://weareoutman.github.io/clockpicker/dist/bootstrap-clockpicker.min.css">
@section('content')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<style>
    .toggle.ios, .toggle-on.ios, .toggle-off.ios { border-radius: 20px; }
    .toggle.ios .toggle-handle { border-radius: 20px; }
    .swal2-cancel btn btn-danger{
       margin-left: 10px !important;
    }
</style>
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    List of all reminder Users Notification 
                </h2>
            </div>
            <div class="body">
                <section class="panel">
                    <header class="panel-heading tab-bg-dark-navy-blue ">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a data-toggle="tab" href="#home">List of reminders</a>
                            </li>
                            <li class="">
                                <a data-toggle="tab" href="#about">Add new reminder</a>
                            </li>

                        </ul>
                    </header>
                    <div class="tab-content">
                        <div id="home" class="tab-pane active">
                            <div class="row">
                                <div class="col-sm-12 col-md-12">


                                    <?php
                                        if(count($data) > 0)
                                            {
                                                ?>
                                        <table id="sim"
                                               class="table table-responsive table-striped table-bordered table12"
                                               width="100%">
                                            <thead>
                                            <tr>

                                                <th><span>Days</span></a></th>
                                                <th><span>Message</span></a></th>
                                                <th><span>Time</span></a></th>
                                                <th><span>Action</span></th>
                                            </tr>
                                            </thead>
                                            <tfoot>
                                            <tr>

                                                <th><span>Days</span></a></th>
                                                <th><span>Message</span></a></th>
                                                <th><span>Time</span></a></th>
                                                <th><span>Action</span></th>
                                            </tr>
                                            </tfoot>
                                            <tbody id="tbody">
                                            <?php $time = date("H:i", $data[0]->time); ?>

                                            <?php $status = $data[0]->run_status; ?>

                                            @foreach ($data as $dt)
                                                <tr>
                                                    <td>{{$dt->days}}</td>
                                                    <td>{{$dt->message}}</td>
                                                    <td>{{date("H:i", $dt->time)}}</td>
                                                    <td>
                                                    <!--<div class="btn-group" id="toggle_event_editing{{$dt->crontime_id}}">-->

                                                        <!--                                                <button type="button" class="btn btn-info">OFF</button>
                                                                                                        <button type="button" class="btn btn-default">ON</button> -->


                                                    <!--                                                <button type="button"  class="btn locked_inactive
                                                {{($dt->run_status == 2) ? 'btn-info' :  'btn-default'}}" onclick="changedata({{$dt->crontime_id}})">OFF</button>
                                                <button type="button" class="btn unlocked_inactive
                                                {{($dt->run_status == 1 || $status == 0) ?  'btn-info' :  'btn-default'}}" onclick="changedata({{$dt->crontime_id}})">ON</button>-->

                                                        <!--</div>-->

                                                        <button type="button" class="btn {{($dt->run_status == 2) ? 'btn-info' :  'btn-default'}}" onclick="changedata({{$dt->crontime_id}},0)">OFF</button>
                                                        <button type="button" class="btn {{($dt->run_status == 1) || ($dt->run_status == 0) ? 'btn-info' :  'btn-default'}}" onclick="changedata({{$dt->crontime_id}},1)">ON</button>
                                                        <button type="button" class="btn btn-default" onclick="edit({{$dt->crontime_id}})">Edit</button>
                                                        <button type="button" class="btn btn-danger" onclick="deletecron({{$dt->crontime_id}})">Delete</button>
                                                    </td>
                                                </tr>

                                            @endforeach

                                            </tbody>

                                        </table>
                                            <?php
                                            }else{
                                            ?>
                                        <table id="sim"
                                               class="table table-responsive table-striped table-bordered table12"
                                               width="100%">
                                            <thead>
                                            <tr>

                                                <th><span>Days</span></a></th>
                                                <th><span>Message</span></a></th>
                                                <th><span>Time</span></a></th>
                                                <th><span>Action</span></th>
                                            </tr>
                                            </thead>
                                            <tfoot>
                                            <tr>

                                                <th><span>Days</span></a></th>
                                                <th><span>Message</span></a></th>
                                                <th><span>Time</span></a></th>
                                                <th><span>Action</span></th>
                                            </tr>
                                            </tfoot>
                                            <tbody id="tbody">

                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td>No Data Avaliable</td>
                                                    <td>

                                                        <!--</div>-->


                                                    </td>
                                                </tr>


                                            </tbody>

                                        </table>
                                            <?php

                                        }
                                    ?>


                                </div>
                            </div>

                        </div>
                        <div id="about" class="tab-pane">
                            <form name="f" method="post" action="{{url('/addcron')}}" >
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-sm-12 col-md-12">
                                        <div class="col-sm-2">
                                            <label>Message</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <textarea  name="message" class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <div class="col-sm-2">
                                            <label>Select Days</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <select name="days" class="form-control">
                                                <option value="Sunday">Sunday</option>
                                                <option value="Monday">Monday</option>
                                                <option value="Tuesday">Tuesday</option>
                                                <option value="Wednesday">Wednesday</option>
                                                <option value="Thursday">Thursday</option>
                                                <option value="Friday">Friday</option>
                                                <option value="Saturday">Saturday</option>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <div class="col-sm-2">
                                            <label>Add Time</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="time" id="single-input">
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <div class="col-sm-2">

                                        </div>
                                        <div class="col-sm-8">
                                            <input type="submit" class="btn btn-success" name="btn" value="Save">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" id="myModal" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form name="f" method="post" action="{{url('/updatecron')}}" >
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-sm-12 col-md-12">
                            <input type="hidden" name="id" id="id" value="">
                            <div class="col-sm-3">
                                <label>Message</label>
                            </div>
                            <div class="col-sm-8">
                                <textarea  name="message" id="message" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="col-sm-12"></div>
                        <div class="col-sm-12"></div>
                        <div class="col-sm-12"></div>
                        <div class="col-sm-12"></div>
                        <div class="col-sm-12"></div>
                        <div class="col-sm-12"></div>
                        <div class="col-sm-12 col-md-12">
                            <div class="col-sm-3">
                                <label>Select Days</label>
                            </div>
                            <div class="col-sm-8">
                                <select name="days" class="form-control" id="days">
                                    <option value="Sunday">Sunday</option>
                                    <option value="Monday">Monday</option>
                                    <option value="Tuesday">Tuesday</option>
                                    <option value="Wednesday">Wednesday</option>
                                    <option value="Thursday">Thursday</option>
                                    <option value="Friday">Friday</option>
                                    <option value="Saturday">Saturday</option>
                                </select>
                            </div>

                        </div>
                        <div class="col-sm-12"></div>
                        <div class="col-sm-12"></div>
                        <div class="col-sm-12"></div>
                        <div class="col-sm-12"></div>
                        <div class="col-sm-12"></div>
                        <div class="col-sm-12"></div>
                        <div class="col-sm-12 col-md-12">
                            <div class="col-sm-3">
                                <label>Add Time</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" class="form-control time" name="time" id="single-input1">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@section('script')

<script type="text/javascript" src="https://weareoutman.github.io/clockpicker/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="https://weareoutman.github.io/clockpicker/assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="https://weareoutman.github.io/clockpicker/dist/bootstrap-clockpicker.min.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/moment.js/2.6.0/moment.min.js" type="text/javascript"></script>
<script src="https://momentjs.com/downloads/moment-timezone.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>

<script type="text/javascript">
                                                var input = $('#single-input').clockpicker({
                                                    placement: 'bottom',
                                                    align: 'left',
                                                    autoclose: true,
                                                    'default': 'now'
                                                });
                                                var input = $('#single-input1').clockpicker({
                                                    placement: 'bottom',
                                                    align: 'left',
                                                    autoclose: true,
                                                    'default': 'now'
                                                });
</script> 
<script type="text/javascript">
    function edit(id)
    {
        var ids = id;
        $.ajax({
            type: 'post',
            data: {id: ids},
            url: "{{url('fadmin/getdata')}}",
            dataType: 'json',
            success: function (response)
            {   
                var time = moment.unix(response.data[0]['time'], 'HH:mm').format('h:mm');
//                var time = moment(t, 'HH:mm').format('h:mm');
                console.log(time);
                console.log(response.data[0]);
                $('#id').val(response.data[0]['crontime_id']);
                $('#message').val(response.data[0]['message']);
                $('.time').val(response.data[0]['time_string']);

                var objSelect = document.getElementById("days");
                setSelectedValue(objSelect, response.data[0]['days']);

                function setSelectedValue(selectObj, valueToSet) {
                    for (var i = 0; i < selectObj.options.length; i++) {
                        if (selectObj.options[i].text == valueToSet) {
                            selectObj.options[i].selected = true;
                            return;
                        }
                    }
                }
//                if (data.success) {
//                    window.location.href = "{{url('fadmin/settings')}}";
//                }
                $('#myModal').modal('show');
            }
        });
    }
    function deletecron(id)
    {
           swal({
                title: 'Are you sure?',
                text: "You won't be Delete this cron?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Delete it!',
                cancelButtonText: 'No, cancel!',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false
            }).then(function (value) {
                console.log(value);
                if(value.value)
                {
                     var ids = id;
                        console.log(id)
                        $.ajax({
                            type: 'post',
                            data: {id: ids},
                            url: "{{url('fadmin/delete')}}",
                            dataType: 'json',
                            success: function (response)
                            {   
//                                    swal(
//                                        'Successfully!',
//                                        'Selected cron has been deleted.',
//                                        'success'
//                                    )
                                if (response.data == 1) {
                                
                                    
                                         window.location.href = "{{url('fadmin/settings')}}";
                                    
//                                   
                                }
                //                $('#myModal').modal('show');
                            }
                        });
                    
                }
                if(value.dismiss)
                {
                     swal(
                            'Cancelled',
                            'Your cron is safe',
                            'error'
                            )
                }
            });
        
//        var ids = id;
//        console.log(id)
//        $.ajax({
//            type: 'post',
//            data: {id: ids},
//            url: "{{url('fadmin/delete')}}",
//            dataType: 'json',
//            success: function (response)
//            {   
//
//                if (response.data == 1) {
//                    window.location.href = "{{url('fadmin/settings')}}";
//                }
////                $('#myModal').modal('show');
//            }
//        });
    }
</script>

<!-- <script>
  var tabs = $('.table').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": "url{{'/GetData'}}"
        });
</script> -->


<script>
    
       function changedata(id,changeid)
       {
        
        var id=id;
        var idd=changeid;
        console.log(id)
        console.log(idd)

      
        
//        var document.getElementById('horseThumb_'+id).className='hand positionLeft'
//        $('#toggle_event_editing button'+id).click(function () {
//            console.log('test');
//            if ($('#toggle_event_editing' + id).hasClass('locked_active') || $('#toggle_event_editing' + id).hasClass('unlocked_inactive')){
//                /* code to do when unlocking */
//                switchonoff = 1
//                // console.log(1)
//            } else {
//                /* code to do when locking */
//                // console.log(0)
//                switchonoff = 0
//            }
//            console.log(switchonoff);

            $.ajax({
                type: 'post',
                data: {idd: idd,userid:id},
                url: "{{url('fadmin/stopcronjob')}}",
                dataType: 'json',
                success: function (data)
                {
                    
                    if (data.success) {
                        
                        window.location.href = "{{url('fadmin/settings')}}";
                    }
                }
            });

            /* reverse locking status */
            $('#toggle_event_editing button').eq(0).toggleClass('locked_inactive locked_active btn-default btn-info');
            $('#toggle_event_editing button').eq(1).toggleClass('unlocked_inactive unlocked_active btn-info btn-default');
//        });
    }
    






</script> 

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

</script>
<!-- Scripts -->
<script src="{{ asset('js/login.js') }}"></script>
@endsection