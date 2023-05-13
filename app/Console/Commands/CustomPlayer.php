<?php

namespace App\Console\Commands;
use App\Models\Player;
use App\Models\Schedule;
use Football;
use App\Models\League;
use App\Models\Team;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class CustomPlayer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ply';

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
        
	$data = Team::where('league_id',9)->get();
        foreach ($data as $model) {


            $bs_url = 'http://api.football-data.org/v2/teams/'.$model->parse_id_v2;

                    $playersJson = $this->getJsonFromArray($bs_url);
                    


                    $player_data = Team::where('parse_id_v2',$playersJson->id)->first();
                    

                        foreach ($playersJson->squad as $player) {
                            Player::query()->updateOrCreate(['parse_id' => $player->id], [
                                'team_id' => $player_data->id,
                                'name' => (string)$player->name,
                                'position' => (string)$player->position,
                                //'jersey_number' => 0,
                                'parse_id' => (int)$player->id,
                                'market_value' => "",
                                'contract_until' => "",
                                'nationality'=>$player->nationality,
                            ]);
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