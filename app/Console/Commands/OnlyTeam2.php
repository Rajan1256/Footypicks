<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Player;
use App\Models\Schedule;
use Football;
use App\Models\League;
use App\Models\Team;
use GuzzleHttp\Client;
class OnlyTeam2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'df:tm2';

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
        $sdata = League::whereIn('parse_id_v2',[2055,2079,2145,2146])->get();

        foreach ($sdata as $model)
        {
        $bs_url = 'http://api.football-data.org/v2/competitions/'.$model->parse_id_v2.'/matches';
        $sc = $this->getJsonFromArray($bs_url);

        foreach ($sc->matches as $el) {

            $team = Team::query()->updateOrCreate(['parse_id_v2' => $el->homeTeam->id,'league_id'=>$model->id], [
                'name' => $el->homeTeam->name,
                'country' => '',
                'league_id' => $model->id,
                'parse_id_v2' => $el->homeTeam->id,
                'parse_id' => 0
            ]);

            $team = Team::query()->updateOrCreate(['parse_id_v2' => $el->awayTeam->id,'league_id'=>$model->id], [
                'name' => $el->awayTeam->name,
                'country' => '',
                'league_id' => $model->id,
                'parse_id_v2' => $el->awayTeam->id,
                'parse_id' => 0
            ]);

//                foreach ($playersJson->squad as $player) {
//                    Player::query()->updateOrCreate(['parse_id' => $player->id], [
//                        'team_id' => $homeModel->id,
//                        'name' => (string)$player->name,
//                        'position' => (string)$player->position,
//                        'jersey_number' => $player->shirtNumber == '' ? 0 : $player->shirtNumber,
//                        'parse_id' => (int)$player->id,
//                        'market_value' => "",
//                        'contract_until' => "",
//                        'nationality' => $player->nationality,
//                    ]);
//                }
            // return response()->json($playersJson);
            // }

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

