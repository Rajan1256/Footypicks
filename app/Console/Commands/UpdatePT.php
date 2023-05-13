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

class UpdatePT extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'df:pt';

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

        //$model = League::all();
        //date_default_timezone_set('Europe/London');
       // foreach($model as $data) {
        http://api.football-data.org/v2/competitions/2145/matches?dateFrom=2018-10-17&dateTo=2018-10-18

      //  $url = 'http://api.football-data.org/v2/competitions/2079/matches';
    //  $url = 'http://api.football-data.org/v2/competitions/2145/matches?dateFrom=2018-10-18&dateTo=2018-10-21';



        $ScheduleJson = $this->get2JsonFromArray($url);


                    foreach ($ScheduleJson->matches as $el) {

                        $ds = Schedule::where('sch_id',$el->id)->get();

                        foreach ($ds as $live_sch)
                        {
                        foreach ($el->goals as $rw) {


                            if ($rw->team->id==$live_sch->team_home_id_parse) {

                                $tmModel = Team::query()->where('id', $live_sch->team_home_id)->first();

                                Goal::query()->updateOrCreate([
                                    'sch_id' => (int)$el->id,
                                    'team_id' => $tmModel->id,
                                    'player_id' => (int)$rw->scorer->id,
                                    'minute' => (int)$rw->minute,
                                ]);
				
				Player::query()->updateOrCreate(['parse_id'=>$rw->scorer->id],
                                    [
                                        'team_id'=>$tmModel->id,
                                        'name'=>$rw->scorer->name,
                                        'parse_id'=>$rw->scorer->id,
                                        'nationality'=>'',
                                        //'jersey_number'=>0
                                    ]);

				
                            } else if ($rw->team->id == $live_sch->team_away_id_parse) {
                                $tmModel2 = Team::query()->where('id', $live_sch->team_away_id)->first();
                                Goal::query()->updateOrCreate([
                                    'sch_id' => (int)$el->id,
                                    'team_id' => $tmModel2->id,
                                    'player_id' => (int)$rw->scorer->id,
                                    'minute' => (int)$rw->minute,
                                ]);

				 Player::query()->updateOrCreate(['parse_id'=>$rw->scorer->id],
                                    [
                                        'team_id'=>$tmModel2->id,
                                        'name'=>$rw->scorer->name,
                                        'parse_id'=>$rw->scorer->id,
                                        'nationality'=>'',
                                        //'jersey_number'=>0
                                    ]);
				
				                            }
                        }

                        }

            }
    //    }


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
