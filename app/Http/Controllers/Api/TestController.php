<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Schedule;
use DateInterval;
use DateTime;
use App\Models\League;
use App\Models\Team;
use App\Models\Player;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use App\Services\Schedule as Service;
use DB;

class TestController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /* =====================  auto delete pridictions for DARE,HEADTOHEAD, LEAGUES Starts Here ===================== */
    public function index()
    {
        $datetime = new DateTime();
        $logEntry = $datetime->format('Y-m-d H:i:s A');
        error_log("[".$logEntry."] TESTT",3, storage_path('logs/liveScoreFinished.log'));
        die;
       
       $model = League::whereIn('parse_id_v2',[2055])->get();
    
        foreach($model as $data) {
            // echo "<pre>";
            //$today_date =date('Y-m-d');
            $from = date( 'Y-m-d',strtotime("-5 hours"));
            $to = date('Y-m-d', strtotime("+1 year"));
            $url = 'http://api.football-data.org/v2/competitions/'.$data->parse_id_v2.'/matches?dateFrom='.$from.'&dateTo='.$to;
            //$url = 'http://api.football-data.org/v2/competitions/2055/matches?dateFrom=2018-09-06&dateTo=2018-10-05';
            $ScheduleJson = $this->get2JsonFromArray($url);

                // return response()->json($ScheduleJson->matches);
                // die;
            $array = [];
            foreach ($ScheduleJson->matches as $el) {

                if(strcmp($el->stage,$data->match_stage)==0) {
                    $array[] = $el->stage;
                }
            }
           

            if(empty($array)) {
         Schedule::where('sch_id',$el->id)->update([
                        'date'=> $el->utcDate
                    ]);
                foreach ($ScheduleJson->matches as $el) {


                    if($el->stage != "") {
//                        $updateLeaguesData = [
//                            'match_stage' => $el->stage
//                        ];

                        if($data->match_stage!="")
                        {

                             League::where('parse_id_v2',$ScheduleJson->competition->id)->update(
                                ['match_stage' => $el->stage]
                            );

                        }
                        if(strcmp($el->stage,$data->match_stage)==0) {
                            $homeModel = Team::query()->where('parse_id_v2', (int) $el->homeTeam->id)->where('league_id',$data->id)->first();

                            $awayModel = Team::query()->where('parse_id_v2', (int) $el->awayTeam->id)->where('league_id',$data->id)->first();

                            $chunks = preg_split('/(T|Z)/', $el->utcDate,-1, PREG_SPLIT_NO_EMPTY);
                            $datetimeConvert = $chunks[0].' '.$chunks[1];

                            Schedule::query()->updateOrCreate([
                                'team_home_id_parse' => (int) $el->homeTeam->id,
                                'team_away_id_parse' => (int) $el->awayTeam->id,
                                'team_home_id' => $homeModel->id,
                                'team_away_id' => $awayModel->id,
                                'date' => (string) $el->utcDate,
                                'matchday' => (int) $el->matchday,
                                'schedule_stage'=>$el->stage,
                                'goals_home_team' => (int) $el->score->fullTime->homeTeam,
                                'goals_away_team' => (int) $el->score->fullTime->awayTeam,
                                'league_id' => (int)$data->id,
                                'start_game_time' => strtotime($datetimeConvert),
                                'status' => Schedule::prepareStatus((string) $el->status),
                            ]);
                        }
                    }
                }
            } else {


                foreach ($ScheduleJson->matches as $el) {

                 Schedule::where('sch_id',$el->id)->update([
                        'date'=> $el->utcDate
                    ]);
                    if(strcmp($el->stage,$data->match_stage)==0) {
                        $homeModel = Team::query()->where('parse_id_v2', (int) $el->homeTeam->id)->where('league_id',$data->id)->first();

                        $awayModel = Team::query()->where('parse_id_v2', (int) $el->awayTeam->id)->where('league_id',$data->id)->first();


                        $chunks3 = preg_split('/(T|Z)/', $el->utcDate,-1, PREG_SPLIT_NO_EMPTY);
                        $datetimeConvert3 = $chunks3[0].' '.$chunks3[1];

                        if($el->matchday=="")
                        {
            
                            if($homeModel && $awayModel) {
                                $dataSave = [
                                    'team_home_id_parse' => (int)$el->homeTeam->id,
                                    'team_away_id_parse' => (int)$el->awayTeam->id,
                                    'team_home_id' => (int)$homeModel->id,
                                    'team_away_id' => (int)$awayModel->id,
                                    'date' => (string)$el->utcDate,
                                    'schedule_stage' => (string) $el->stage,
                                    'goals_home_team' => (int)$el->score->fullTime->homeTeam,
                                    'goals_away_team' => (int)$el->score->fullTime->awayTeam,
                                    'league_id' => (int)$data->id,
                                    'sch_id' => $el->id,
                                    'start_game_time' => strtotime($datetimeConvert3),
                                    'status' => Schedule::prepareStatus((string)$el->status),
                                ];

                                if(!$homeModel->id || !$awayModel->id) {
                                    $this->log('Fixtures not found Models' . " id home Team= $el->homeTeam->id id away = $el->awayTeam->id");
                                }


                                $service = new Service();
                                $service->setWhere([
                                    'team_home_id_parse' => $dataSave['team_home_id_parse'],
                                    'team_away_id_parse' => $dataSave['team_away_id_parse']
                                ]);

                                $service->getOne();
                                if($service->getModel()) {
                                    if ($dataSave['status'] == Schedule::FINISHED) {
                                        $this->log('Update Results ' . $service->getModel()->id . ' Schedule');
                                        $service->scheduleFinish($dataSave);
                                    }
                                }


                                $dataSave['team_home_id'] = $homeModel->id;
                                $dataSave['team_away_id'] = $awayModel->id;
                                Schedule::query()->updateOrCreate([
                                    'team_home_id_parse' => $dataSave['team_home_id_parse'],
                                    'team_away_id_parse' => $dataSave['team_away_id_parse'],
                                    'sch_id' => $dataSave['sch_id']
                                ], $dataSave);
                                if(isset($dataSave['date'], $service->getModel()->id))
                                    $this->log($service->getModel()->id . "  " . $dataSave['date']);

                            }
                        }
                        else
                        {
                            if($homeModel && $awayModel) {
                                $dataSave = [
                                    'team_home_id_parse' => (int)$el->homeTeam->id,
                                    'team_away_id_parse' => (int)$el->awayTeam->id,
                                    'team_home_id' => (int)$homeModel->id,
                                    'team_away_id' => (int)$awayModel->id,
                                    'date' => (string)$el->utcDate,
                                    'matchday' => (int)$el->matchday,
                                    'goals_home_team' => (int)$el->score->fullTime->homeTeam,
                                    'goals_away_team' => (int)$el->score->fullTime->awayTeam,
                                    'league_id' => (int)$data->id,
                                    'sch_id' => $el->id,
                                    'start_game_time' => strtotime($datetimeConvert3),
                                    'status' => Schedule::prepareStatus((string)$el->status),
                                ];

                                if(!$homeModel->id || !$awayModel->id) {
                                    $this->log('Fixtures not found Models' . " id home Team= $el->homeTeam->id id away = $el->awayTeam->id");
                                }


                                $service = new Service();
                                $service->setWhere([
                                    'team_home_id_parse' => $dataSave['team_home_id_parse'],
                                    'team_away_id_parse' => $dataSave['team_away_id_parse']
                                ]);

                                $service->getOne();
                                if($service->getModel()) {
                                    if ($dataSave['status'] == Schedule::FINISHED) {
                                        $this->log('Update Results ' . $service->getModel()->id . ' Schedule');
                                        $service->scheduleFinish($dataSave);
                                    }
                                }


                                $dataSave['team_home_id'] = $homeModel->id;
                                $dataSave['team_away_id'] = $awayModel->id;
                                Schedule::query()->updateOrCreate([
                                    'team_home_id_parse' => $dataSave['team_home_id_parse'],
                                    'team_away_id_parse' => $dataSave['team_away_id_parse'],
                                    'sch_id' => $dataSave['sch_id']
                                ], $dataSave);
                                if(isset($dataSave['date'], $service->getModel()->id))
                                    $this->log($service->getModel()->id . "  " . $dataSave['date']);

                            }
                        }

                    }
                }
            }
        }
    }

    private function get2JsonFromArray($url = '')
    {
        $header = array('headers' => array('X-Auth-Token' => 'ae55fbb958394440857a9a9da0c6a1af'));
        $response = $this->client->get($url, $header);
        $json = json_decode($response->getBody()->getContents());
        if ($response->getStatusCode() !== 200) {
            var_dump($json);
            die();
        }

        return $json;
    }
    /* =====================  Football-data.org V2 api for schedules. ENDS HERE ===================== */
}
