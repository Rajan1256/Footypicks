<?php

namespace App\Console\Commands;

use App\Models\Schedule;
use DateInterval;
use DateTime;
use App\Models\League;
use App\Models\Team;
use App\Models\Player;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use DB;
use App\Models\Goal;
use App\Services\Schedule as Service;

class Europa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'df:er';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse Italian Football League';

    protected $client;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->client = new Client();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {



        $model = League::where('parse_id_v2',2146)->get();

        foreach($model as $data) {
            // echo "<pre>";
            $today_date =date('2018-10-06');
            //$from = date( 'Y-m-d', strtotime( $today_date . ' -1 day' ) );
            $from = date( 'Y-m-d',strtotime("-5 hours"));
            $to = date('Y-m-d', strtotime("+1 year"));
            $url = 'http://api.football-data.org/v2/competitions/'.$data->parse_id_v2.'/matches?dateFrom='.$from.'&dateTo='.$to;
            //$url='http://api.football-data.org/v2/competitions/'.$data->parse_id_v2.'/matches?matchday=4';
            //$url = 'http://api.football-data.org/v2/competitions/'.$data->parse_id_v2.'/matches?dateFrom=2018-08-19&dateTo=2018-08-20';
            $ScheduleJson = $this->get2JsonFromArray($url);

            $array = [];
            foreach ($ScheduleJson->matches as $el) {
                if($el->matchday == $data->current_matchday) {
                    $array[] = $el->matchday;
                }
            }

            if(empty($array)) {
                foreach ($ScheduleJson->matches as $el) {


                    if($el->matchday != "") {
                        $updateLeaguesData = [
                            'current_matchday' => $data->current_matchday+1
                        ];
                        $updateLeagues = League::where('id', $data->id)->update($updateLeaguesData);

                        if($el->matchday == $data->current_matchday+1) {
                            $homeModel = Team::query()->where('parse_id_v2', (int) $el->homeTeam->id)->first();

                            $awayModel = Team::query()->where('parse_id_v2', (int) $el->awayTeam->id)->first();

                            $chunks = preg_split('/(T|Z)/', $el->utcDate,-1, PREG_SPLIT_NO_EMPTY);
                            $datetimeConvert = $chunks[0].' '.$chunks[1];


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
                                    'start_game_time' => strtotime($datetimeConvert),
                                    'status' => Schedule::prepareStatus((string)$el->status),
                                ];

                                Schedule::where('sch_id',$el->id)->update([
                                    'date'=>$el->utcDate
                                ]);
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
            } else

            {
                foreach ($ScheduleJson->matches as $el) {


                    if($el->matchday == $data->current_matchday) {
                        $homeModel = Team::query()->where('parse_id_v2', (int) $el->homeTeam->id)->first();

                        $awayModel = Team::query()->where('parse_id_v2', (int) $el->awayTeam->id)->first();

                        $chunks3 = preg_split('/(T|Z)/', $el->utcDate,-1, PREG_SPLIT_NO_EMPTY);
                        $datetimeConvert3 = $chunks3[0].' '.$chunks3[1];

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
//                            foreach ($el->goals as $rw)
//                            {
//                                $tmModel = Team::query()->where('parse_id_v2', (int) $rw->team->id)->first();
//                                Goal::query()->updateOrCreate([
//                                    'sch_id' => (int) $el->id,
//                                    'team_id' => $tmModel->id,
//                                    'player_id' => (int)$rw->scorer->id,
//                                    'minute'=>(int)$rw->minute,
//                                ]);
//                            }
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

        /* For delete pending predicts if schedule is over */
//        $clear_data = League::all();
//        foreach ($clear_data as $row)
//        {
//            $collectionsHTH = League::where('parse_id_v2', $row->parse_id_v2)->first();
//        }
//        $collectionsHTH1 = DB::table('schedules')->where('status',1)->get();
//
//
//        foreach ($collectionsHTH1 as $colle) {
//            $HTH = DB::table('head_to_heads')->where([
//                'schedule_id' => $colle->id,
//            ])->get();
//            foreach ($HTH as $a) {
//
//                if($a->status==2)
//                {
//                    DB::table('head_to_heads')->where([
//                        'id' => $a->id])->delete();
//                }
//
//                if($a->status==0)
//                {
//                    DB::table('head_to_heads')->where([
//                        'id' => $a->id])->delete();
//                }
//
//            }
//
//        }


//        $games = DB::table('games')->where('status', 0)->get();
//
//
//        foreach ($games as $game) {
//            $Leagues = DB::table('users_in_games')->where([
//                'game_id' => $game->id,
//                'status' => 0
//            ])->get();
//
//
//            foreach ($Leagues as $League) {
//                 DB::table('users_in_games')->where('game_id',$League->game_id)->delete();
//            }

        /* delete pending predicts END */

//        $url = 'http://api.football-data.org/v2/competitions/';
//        $json = $this->get2JsonFromArray($url);
//        $leagues = [
//            2021 => "PremierLeague.png",
//            2015 => "Ligue_1_Logo.png",
//            2002=>  "bundesliga.png",
//            2014=>  "PrimeraDivision.png",
//            2019=>  "LegaSerieA.png",
//            2001=> "ChampionsLeague.png",
//            2000=> "2018_FIFA_World_Cup.png",
//        ];
//
//        foreach ($json->competitions as $data) {
//
//            if (!isset($leagues[$data->id])) {
//                continue;
//            }
//            $league_id = $data->id;
//
//
//            $model = League::query()->active()->where('parse_id_v2', $league_id)->first();
//            if(!$model) {
//                $model = new League();
//            }
//
//            $from =date('Y-m-d');
//            $to = date('Y-m-d', strtotime("+1 year"));
//            // $sc = $this->getJsonFromArray('http://api.football-api.com/2.0/matches?comp_id='.$data->id.'&'
//            //     . 'from_date=' . $from . '&to_date=' . $to);
//            $sc = $this->get2JsonFromArray('http://api.football-data.org/v2/competitions/'.$data->id.'/matches?dateFrom='.$from.'&dateTo='.$to);
//            $count = count($sc);
//            if($count === 0) {
//                return;
//            }
//
//            $array = [];
////            foreach ($sc as $el) {
////               // echo "<pre>";
////                //print_r($el);
////
////            }
//
//
//            if(empty($array)) {
//                foreach ($sc->matches as $el) {
//
//
//                    if($el->matchday != "") {
//                        /*$updateLeaguesData = [
//                            'current_matchday' => $model->current_matchday+1
//                        ];
//                        $updateLeagues = League::where('id', $model->id)->update($updateLeaguesData);*/
//
//                        if($el->matchday == $model->current_matchday+1) {
//                            echo "NEW".$el->id;
//                            echo "NOT blank".$model->name;
//                            $homeModel = Team::query()->where('parse_id_v2', (int) $el->homeTeam->id)->first();
//
//                            $awayModel = Team::query()->where('parse_id_v2', (int) $el->awayTeam->id)->first();
//
//                            $chunks1 = preg_split('/(T|Z)/', $el->utcDate,-1, PREG_SPLIT_NO_EMPTY);
//                            $datetimeConvert1 = $chunks1[0].' '.$chunks1[1];
////                            $a = explode(".", $el->utcDate);
////                            if($el->time == "TBA") {
////                                $time = "";
////                            } else {
////                                $time = $el->time;
////                            }
////                            $data = $a[2].'-'.$a[1].'-'.$a[0].'T'.$el->time.'Z';
//
//                            Schedule::query()->updateOrCreate([
//                                'team_home_id_parse' => (int) $el->homeTeam->id,
//                                'team_away_id_parse' => (int) $el->awayTeam->id,
//                                'team_home_id' => $homeModel->id,
//                                'team_away_id' => $awayModel->id,
//                                'date' => (string) $el->utcDate,
//                                'matchday' => (int) $el->matchday,
//                                'goals_home_team' => (int) $el->score->fullTime->homeTeam,
//                                'goals_away_team' => (int) $el->score->fullTime->awayTeam,
//                                'league_id' => (int)$model->id,
//                                'start_game_time' => strtotime($datetimeConvert1),
//                                'status' => Schedule::prepareStatus((string) $el->status),
//                            ]);
//                        }
//                    }
//                }
//            } else {
//                foreach ($sc->matches as $el) {
//                    if($el->matchday == $model->current_matchday) {
//                        echo "OLD".$el->id;
//                        echo "NOT blank".$model->name;
//                        $homeModel = Team::query()->where('parse_id_v2', (int) $el->homeTeam_id)->first();
//
//                        $awayModel = Team::query()->where('parse_id_v2', (int) $el->awayTeam_id)->first();
//                        $chunks2 = preg_split('/(T|Z)/', $el->utcDate,-1, PREG_SPLIT_NO_EMPTY);
//                        $datetimeConvert2 = $chunks2[0].' '.$chunks2[1];
////                        $a = explode(".", $el->utcDate);
////                        if($el->time == "TBA") {
////                            $time = "";
////                        } else {
////                            $time = $el->time;
////                        }
////                        $data = $a[2].'-'.$a[1].'-'.$a[0].'T'.$el->time.'Z';
////                        $datetimeConvert =$a[2].'-'.$a[1].'-'.$a[0]." ".$time;
//                        //echo strtotime($datetimeConvert)."<br/>";*/
//                         Schedule::query()->updateOrCreate([
//                             'team_home_id_parse' => (int) $el->homeTeam->id,
//                             'team_away_id_parse' => (int) $el->awayTeam->id,
//                             'team_home_id' => $homeModel->id,
//                             'team_away_id' => $awayModel->id,
//                             'date' => (string) $el->utcDate,
//                             'matchday' => (int) $el->matchday,
//                             'goals_home_team' => (int) $el->score->fullTime->homeTeam,
//                             'goals_away_team' => (int) $el->score->fullTime->awayTeam,
//                             'league_id' => (int)$model->id,
//                             'start_game_time' => strtotime($datetimeConvert2),
//                             'status' => Schedule::prepareStatus((string) $el->status),
//                         ]);
//                    }
//                }
//            }
//        }
    }

    private function get2JsonFromArray($url = '')
    {
        $header = array('headers' => array('X-Auth-Token' => 'ae55fbb958394440857a9a9da0c6a1af'));
        $response = $this->client->get($url, $header);
        $json = json_decode($response->getBody()->getContents());
        if ($response->getStatusCode() !== 200) {
            $finalData = "\n".$json."\n"."\n"."\n";

            $datetime = new DateTime();
            $logEntry = $datetime->format('Y-m-d H:i:s A');
            error_log("[".$logEntry."]".$finalData,3, storage_path('logs/footballSchedule.log'));
            var_dump($json);
            die();
        }

        return $json;
    }

    /*private function getJsonFromArray($url = '')
    {
        if(!strpos($url,'?')) {
            $url .= '?';
        }
        $response = $this->client->get($url . '&Authorization=565ec012251f932ea4000001968456b7c7904f3068e6eef6b4d30f5f');
        $json = json_decode($response->getBody()->getContents());
        if ($response->getStatusCode() !== 200) {
            var_dump($json);
            // die();
        }

        return $json;
    }*/

    private function log($message){

        print_r($message . "\n");
    }
}
