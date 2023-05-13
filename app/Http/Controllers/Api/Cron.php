<?php

namespace App\Http\Controllers\Api;

use App\Models\HeadToHead;
use App\Models\HeadToHeadInvite;
use App\Models\NotificationModel;
use App\Models\PushToken;
use App\Models\UserStat;
use App\Models\User;
use App\Post_count;
use App\User_Feed_Count;
use App\Models\UserInGame;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use DB;
use App\Models\Goal;
use App\Models\Schedule;
use DateInterval;
use DateTime;
use App\Models\League;
use App\Models\Team;
use App\Models\Player;
use App\Services\Schedule as Service;

class Cron extends Controller {

    public function statusSchedule(Request $request) {

        $QR = "select * from leagues where parse_id_v2 in(2079,2055, 2146)";
        $Leag = DB::select($QR);

        $current_dt = gmdate("Y-m-d\TH:i:s\Z");
        foreach ($Leag as $LG) {

//            $from = date('Y-m-d', strtotime("-4 month"));
            $from = date('Y-m-d', strtotime("-3 hours"));
//            $to = date('Y-m-d');

            $to = date('Y-m-d', strtotime("+3 month"));


            $url = 'http://api.football-data.org/v2/competitions/' . $LG->parse_id_v2 . '/matches?dateFrom=' . $from . '&dateTo=' . $to;
            $ScheduleJson = $this->get2JsonFromArray($url);

//            $array = [];
//            foreach ($ScheduleJson['matches'] as $SCH) {
//
//                //Update call on Schedule
//
////                if (strcmp($SCH['stage'], $LG->match_stage) == 0) {
////                    $array[] = $SCH['stage'];
////                }
//            }


            foreach ($ScheduleJson['matches'] as $SCH) {
                if ($SCH['stage'] != "") {
//                    if ($LG->match_stage != "") {
//                        if ($LG->match_stage != $SCH['stage'] && $SCH['status'] == "SCHEDULED" && $SCH['utcDate'] >= $current_dt) {
//
//
//                            //Update Call on League 
////                                echo $ScheduleJson['competition']['id'];
////                                echo "<br>";
//                        }
//                    }
//                        $chunks = preg_split('/(T|Z)/', $SCH['utcDate'], -1, PREG_SPLIT_NO_EMPTY);
//                        $datetimeConvert = $chunks[0] . ' ' . $chunks[1];
//                        echo $SCH['homeTeam']['id'];
//                        echo $SCH['awayTeam']['id'];



                    if ($SCH['status'] == 'FINISHED') {





//                        if (strcmp($SCH['stage'], $LG->match_stage) == 0) {
////
                        $QRY = "select * from schedules where sch_id='" . $SCH['id'] . "' and status=1";
                        $RES = DB::select($QRY);

                        foreach ($RES as $dt) {



//                            $data = "select * from head_to_heads where schedule_id='" . $dt->id . "'";
//                            $rd = DB::select($data);
//                            echo "<pre>";
//                            print_r($rd);


                            $data = "update head_to_heads set status=5 where schedule_id='" . $dt->id . "'";
                            $rd = DB::update($data);
                            echo "schedule_id  :- " . $dt->id;
                            echo "<br>";
                            echo "Status  :-  5 ";
                            echo "<br>";
                            echo "<br>";
                        }
//                        }
                    }

//                   
                }
            }
        }
//            echo "<pre>";
//            print_r($SCH);
    }

    private function get2JsonFromArray($url) {

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Auth-Token: ae55fbb958394440857a9a9da0c6a1af'));
        $response = curl_exec($ch);
        $json_data = json_decode($response, true);
        return $json_data;
    }

}
