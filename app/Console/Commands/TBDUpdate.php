<?php

namespace App\Console\Commands;

use App\Models\Schedule;
use App\Models\League;
use DateTime;
use GuzzleHttp\Client;
use App\Models\Team;
use App\Services\Schedule as Service;
use Illuminate\Console\Command;

class TBDUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'df:tbd';

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
        $model = League::whereIn('parse_id_v2',[2021,2015,2002,2014,2019,2001])->get();

        $from = date( 'Y-m-d');
        $from = date( 'Y-m-d',strtotime("-1 day"));
        $to = date('Y-m-d', strtotime("+1 day"));

        foreach ($model as $data) {

            $url = 'http://api.football-data.org/v2/competitions/'.$data->parse_id_v2.'/matches?dateFrom='.$from.'&dateTo='.$to;
            $ScheduleJson = $this->get2JsonFromArray($url);

            foreach ($ScheduleJson->matches as $el) {

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

    private function get2JsonFromArray($url = '')
    {
        $header = array('headers' => array('X-Auth-Token' => 'f735cdcb9210478bbe6c8cc9e8941537'));
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


    private function log($message){

        print_r($message . "\n");
    }
}