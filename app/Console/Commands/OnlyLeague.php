<?php

namespace App\Console\Commands;


use Football;
use App\Models\League;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
class OnlyLeague extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'df:lg';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
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
     */
    public function handle()
    {
        $leagues = [

            //2021 => "PremierLeague.png",
	    //2001=> "UEFA_Champions_League.png",
            //2015 => "Ligue_1_Logo.png",
            //2002=>  "bundesliga.png",
            //2014=>  "LaLiga.png",
            //2019=>  "LegaSerieA.png",
          //2000=> "2018_FIFA_World_Cup.png",
	 2146=>  "UEFA_Europa_League.png",
            2055=>  "FA_Cup.png",
            2079=>  "Copa_del_Rey.png",
            2145=>  "MLS.png",


        ];


        $url = 'http://api.football-data.org/v2/competitions/';
        $json = $this->getJsonFromArray($url);


        foreach ($json->competitions as $data) {


            // print_r($data);
            //echo $data->id;
            if (!isset($leagues[$data->id])) {
                continue;
            }
            $league_id = $data->id;
            $this->log('Parse ' . $data->name . ' league');
            $tem = 'http://api.football-data.org/v2/competitions/' . $league_id . '/teams';
            $mth = 'http://api.football-data.org/v2/competitions/' . $league_id . '/matches';
            $teamsJson = $this->getJsonFromArray($tem);
            $gameJson = $this->getJsonFromArray($mth);
            foreach ($gameJson->matches as $dt) {
                //echo $dt->stage;
                //echo $dt->status;




                    if ($league_id == '2014') {

                        $mydata = [
                            'caption' => 'La Liga',
                            'name' => 'La Liga',
                            'teams_count' => $teamsJson->count,
                            'games_count' => $gameJson->count,
                            //'current_matchday' => $data->currentSeason->currentMatchday,
                            //'match_stage'=>$dt->stage,
                            'matchdays_count' => 38,
                            // 'league_parse_id' => ,
                            'parse_id_v2' => $league_id,
                            'cover' => $leagues[$league_id],
                            'last_updated' => $data->lastUpdated
                        ];
                    }


                    if ($data->currentSeason->currentMatchday=='') {

                        $mydata = [
                            'caption' => $data->name,
                            'name' => $data->name,
                            'teams_count' => $teamsJson->count,
                            'games_count' => $gameJson->count,
                            //'current_matchday' => $data->currentSeason->currentMatchday,
                            'match_stage'=>$dt->stage,
                            'matchdays_count' => 38,
                            // 'league_parse_id' => ,
                            'parse_id_v2' => $league_id,
                            'cover' => $leagues[$league_id],
                            'last_updated' => $data->lastUpdated
                        ];
                    }
                    else
                    {
                        $mydata = [
                           // 'caption' => $data->name,
                           // 'name' => $data->name,
                            'teams_count' => $teamsJson->count,
                            'games_count' => $gameJson->count,
                            'current_matchday' => $data->currentSeason->currentMatchday,
                            //'match_stage'=>$dt->stage,
                            'matchdays_count' => 38,
                            // 'league_parse_id' => ,
                            'parse_id_v2' => $league_id,
                            'cover' => $leagues[$league_id],
                            'last_updated' => $data->lastUpdated
                        ];
                    }


//           echo "<pre>";
//           echo print_r($mydata);
//           echo "</pre>";

                    League::query()->updateOrCreate(['parse_id_v2' => $league_id], $mydata);

            }
        }
    }

    private function getJsonFromArray($url = '')
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

    private function log($message){

        print_r($message . "\n");
    }
}
