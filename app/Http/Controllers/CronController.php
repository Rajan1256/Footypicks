<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Events\CreateNotification;
use DB;
use DateTime;

class CronController extends Controller {

    public function reminderNotification(Request $request) {

        User::chunk(100, function ($users) {
            foreach ($users as $user) {
                $createEvent = new CreateNotification();
                $createEvent->predectionReminder($user->id);
                event($createEvent);
            }
        });
    }

    public function matchcron(Request $request) {


        //$sql = "SELECT * FROM crontime";
        //$data = DB::select($sql);
	$data = DB::table('crontime')->get();
        $currentday = date('l');
        $currenttime = time();
        $date = new DateTime;
        $date->setTimestamp($currenttime);
        $forgot_valid = $date->format('H:i');

        foreach ($data as $dt) {

            $cronid = $dt->crontime_id;
            $crondays = $dt->days;
            $runstatus = $dt->run_status;
            $crontime = $dt->time;

            $this->log('Currnt Time:- ' . $currenttime);
            echo "<br>";
            $this->log('Cron Run Day:- ' . $crondays);
            echo "<br>";
            $this->log('And Time:- ' . $crontime);
            echo "<br>";
            $this->log('Run Status:- ' . $runstatus);
            echo "<br>";
            if ($runstatus == 0) {
                if ($currentday == $crondays) {
                    if ($forgot_valid === $dt->time_string) {

                        //Send Notification Code
                        User::chunk(100, function ($users) {
                            foreach ($users as $user) {
//                        
                                $createEvent = new CreateNotification();
                           // $createEvent->allReminder(88);
                                $createEvent->allReminder($user->id);
                                event($createEvent);
                            }
                        });

//
                        $data = "update crontime set run_status =1 where crontime_id =" . $cronid;
                        DB::update($data);

                        $this->log('Cron run Success:- ' . $crontime . ",");
                        echo "<br>";
                        echo "<br>";
                        echo "<br>";
                    }
                }
            } else if ($runstatus == 1) {
                $startDate = time();
                $t = date('Y-m-d', strtotime('+1 day', $startDate));
                $nextdate = date("l", strtotime($t));
                if ($currentday != $crondays) {
                    $data = "update crontime set run_status =0 where crontime_id =" . $cronid;
                    DB::update($data);
                }
            } else {
                $this->log('Cron run Deactive:- ' . $crontime . ",");
            }
        }
    }

    private function log($message) {
        print_r($message . "\n");
    }

}
