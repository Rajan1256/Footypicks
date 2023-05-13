<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Player;
use App\Models\League;
use GuzzleHttp\Client;
class UpdateShirt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'df:ts';

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
       // $data = League::where('id',10)->get();
	$data = League::get();


        foreach ($data as $model)
        {


            $bs_url = 'http://api.football-data.org/v2/competitions/'.$model->parse_id_v2.'/matches';
            $sc = $this->getJsonFromArray($bs_url);


            foreach ($sc->matches as $el)
            {

                foreach ($el->homeTeam->lineup as $pl)
                {
                    Player::where('parse_id',$pl->id)->update([
                        'jersey_number' => $pl->shirtNumber,
                    ]);
                }

                foreach ($el->homeTeam->bench as $pl)
                {
                    Player::where('parse_id',$pl->id)->update([
                        'jersey_number' => $pl->shirtNumber,
                    ]);
                }

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
}
