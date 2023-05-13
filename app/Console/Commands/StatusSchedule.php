<?php

namespace App\Console\Commands;

use App\Models\Goal;
use App\Models\Schedule;
use DateInterval;
use DateTime;
use App\Models\League;
use App\Models\Team;
use App\Models\Player;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use App\Services\Schedule as Service;

class StatusSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'df:st';

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
        $model = League::whereIn('parse_id_v2',[2145,2079,2055, 2146])->get();

       // date_default_timezone_set('Europe/London');
        foreach($model as $data) {
            // echo "<pre>";
            //$today_date =date('Y-m-d');
            //$from = date( 'Y-m-d', strtotime( $today_date . ' -1 day' ) );
             $from = date('Y-m-d');
            //$from = '2018-09-04';
            $from = date( 'Y-m-d',strtotime("-3 hours"));
            $to = date('Y-m-d', strtotime("+3 month"));
            $url = 'http://api.football-data.org/v2/competitions/'.$data->parse_id_v2.'/matches?dateFrom='.$from.'&dateTo='.$to;
            //$url = 'http://api.football-data.org/v2/competitions/2055/matches?dateFrom=2018-09-06&dateTo=2018-10-05';
            $ScheduleJson = $this->get2JsonFromArray($url);
            $current_dt =  gmdate("Y-m-d\TH:i:s\Z");


            $array = [];
            foreach ($ScheduleJson->matches as $el) {
            	Schedule::where('sch_id',$el->id)->update([ 'date' => (string) $el->utcDate]);

                if(strcmp($el->stage,$data->match_stage)==0) {
                    $array[] = $el->stage;
                }
            }

            if(empty($array)) {
                foreach ($ScheduleJson->matches as $el) {
                    if($el->stage != "") {

                        if ($data->match_stage != "")
                        {
                            if($data->match_stage!=$el->stage && $el->status=="SCHEDULED" && $el->utcDate>=$current_dt)
                            {

                                League::where('parse_id_v2',$ScheduleJson->competition->id)->update(
                                        ['match_stage' => $el->stage]
                                    );
                            }

                        }

                        if(strcmp($el->stage,$data->match_stage)==0) {
                            $homeModel = Team::query()->where('parse_id_v2', (int) $el->homeTeam->id)->where('league_id',$data->id)->first();

                            $awayModel = Team::query()->where('parse_id_v2', (int) $el->awayTeam->id)->where('league_id',$data->id)->first();

                            $chunks = preg_split('/(T|Z)/', $el->utcDate,-1, PREG_SPLIT_NO_EMPTY);
                            $datetimeConvert = $chunks[0].' '.$chunks[1];

                            Schedule::query()->updateOrCreate(['sch_id' => $el->id],[
                                'team_home_id_parse' => (int) $el->homeTeam->id,
                                'team_away_id_parse' => (int) $el->awayTeam->id,
                                'team_home_id' => $homeModel->id,
                                'team_away_id' => $awayModel->id,
                                'date' => (string) $el->utcDate,
                                'matchday' => (int) $el->matchday,
                                'schedule_stage'=>$el->stage,
                                'sch_id'=>(int)$el->id,
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

                    if(strcmp($el->stage,$data->match_stage)==0) {
                        $homeModel = Team::query()->where('parse_id_v2', (int) $el->homeTeam->id)->where('league_id',$data->id)->first();

                        $awayModel = Team::query()->where('parse_id_v2', (int) $el->awayTeam->id)->where('league_id',$data->id)->first();


                        $chunks3 = preg_split('/(T|Z)/', $el->utcDate,-1, PREG_SPLIT_NO_EMPTY);
                        $datetimeConvert3 = $chunks3[0].' '.$chunks3[1];

                        if($el->matchday==null)
                        {

                        	Schedule::query()->updateOrCreate(['sch_id' => $el->id],[
                                'team_home_id_parse' => (int) $el->homeTeam->id,
                                'team_away_id_parse' => (int) $el->awayTeam->id,
                                'team_home_id' => $homeModel->id,
                                'team_away_id' => $awayModel->id,
                                'date' => (string) $el->utcDate,
                                'matchday' => (int) $el->matchday,
                                'schedule_stage'=>$el->stage,
                                'sch_id'=>(int)$el->id,
                                'goals_home_team' => (int) $el->score->fullTime->homeTeam,
                                'goals_away_team' => (int) $el->score->fullTime->awayTeam,
                                'league_id' => (int)$data->id,
                                'start_game_time' => strtotime($datetimeConvert3),
                                'status' => Schedule::prepareStatus((string) $el->status),
                            ]);
                           
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
                                // Schedule::query()->updateOrCreate([
                                //     'team_home_id_parse' => $dataSave['team_home_id_parse'],
                                //     'team_away_id_parse' => $dataSave['team_away_id_parse'],
                                //     'sch_id' => $dataSave['sch_id']
                                // ], $dataSave);
                                if(isset($dataSave['date'], $service->getModel()->id))
                                    $this->log($service->getModel()->id . "  " . $dataSave['date']);

                            }
                        }
                        else
                        {

                        	Schedule::query()->updateOrCreate(['sch_id'=>$el->id],[
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
                                'sch_id'=>(int)$el->id,
                                'start_game_time' => strtotime($datetimeConvert3),
                                'status' => Schedule::prepareStatus((string) $el->status),
                            ]);
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
                                // Schedule::query()->updateOrCreate([
                                //     'sch_id' => $dataSave['sch_id'],
                                //     'team_home_id_parse' => $dataSave['team_home_id_parse'],
                                //     'team_away_id_parse' => $dataSave['team_away_id_parse'],
                                // ], $dataSave);
                                if(isset($dataSave['date'], $service->getModel()->id))
                                    $this->log($service->getModel()->id . "  " . $dataSave['date']);

                            }
                        }

                    }
                }
            }
        }


        Schedule::where('date','<',$current_dt)
            ->where('status',5)->where('status',0)->where('status',4)->delete();
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
//
//
//
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
//            if($el->stage)
//             {
//                foreach ($sc->matches as $el) {
//                    if($el->stage == $model->match_stage) {
//                       // echo "OLD".$el->id;
//                        //echo "NOT blank".$model->name;
//                        $homeModel = Team::query()->where('parse_id_v2', (int) $el->homeTeam->id)->first();
//
//                        $awayModel = Team::query()->where('parse_id_v2', (int) $el->awayTeam->id)->first();
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
//                        Schedule::query()->updateOrCreate([
//                            'team_home_id_parse' => (int) $el->homeTeam->id,
//                            'team_away_id_parse' => (int) $el->awayTeam->id,
//                            'team_home_id' => $homeModel->id,
//                            'team_away_id' => $awayModel->id,
//                            'date' => (string) $el->utcDate,
//                            'matchday' => (int) $el->matchday,
//                            'goals_home_team' => (int) $el->score->fullTime->homeTeam,
//                            'goals_away_team' => (int) $el->score->fullTime->awayTeam,
//                            'league_id' => (int)$model->id,
//                            'start_game_time' => strtotime($datetimeConvert2),
//                            'status' => Schedule::prepareStatus((string) $el->status),
//                        ]);
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
