<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller as BaseController;
use App\Models\League;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Models\User;
use App\Events\CreateNotification;
use DB;

class AddCronJob extends BaseController {


    public function __construct() {
        $this->middleware('auth');
    }

    public function index() {

//        $collection = Team::query()->with('league')->get();
//        return view('admin.teams.index', ['collection' => $collection]);
    }

    public function addcron(Request $request) {



        $cronruntime = strtotime($request->time);
        $runstatus = 0;


        DB::table('crontime')->insert(
                array(
                    'message' => trim($request->message),
                    'days' => $request->days,
                    'time' => $cronruntime,
                    'run_status' => $runstatus,
                    'time_string' => $request->time,
                )
        );

        return redirect(route('a:settings'));
    }

    public function update(Request $request) {

        $sql = "SELECT * FROM crontime where crontime_id =" . $request->id;
        $get = DB::select($sql);

        $cronstatus = $get[0]->run_status;

        $cronruntime = strtotime($request->time);
        $runstatus = 0;

//        $data = "update crontime set message='" . trim($request->message) . "',days ='" . $request->days . "' ,time ='" . $cronruntime . "',run_status='" . $runstatus . "',time_string='" . $request->time . "' where crontime_id =" . $request->id;
//        DB::update($data);

        DB::table('crontime')->where('crontime_id',$request->id)->update([
            'message'=>$request['message'],
            'days'=>$request['days'],
            'time'=>$cronruntime,
            'run_status'=>$runstatus,

        ]);

        if ($cronstatus == 2) {

            $data1 = "update crontime set run_status=2 where crontime_id =" . $request->id;
            DB::update($data1);
        }
        return redirect(route('a:settings'));
    }

    public function stopcronjob(Request $request) {

        $updatestatus = "";
        if ($request->idd == 0) {
            $updatestatus = 2;
        } else {
            $updatestatus = 0;
        }

        $data = "update crontime set run_status='" . $updatestatus . "' where crontime_id =" . $request->userid;
        DB::update($data);
        echo json_encode(array('success' => true, 'status' => 200));
    }

    public function getdata(Request $request) {

        $sql = "SELECT * FROM crontime where crontime_id='" . $request->id . "'";
        $get = DB::select($sql);
        echo json_encode(array('success' => true, 'data' => $get, 'status' => 200));
    }

    public function delete(Request $request) {

   
        $sql = "Delete FROM crontime where crontime_id='" . $request->id . "'";
        $get = DB::delete($sql);
        echo json_encode(array('success' => true, 'data' => $get, 'status' => 200));
    }

}
