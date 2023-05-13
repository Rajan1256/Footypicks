<?php

namespace App\Console\Commands;

use App\Models\Player;
use Football;
use App\Models\League;
use App\Models\Team;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class ParseLeaguePay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse_v2:lg';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse Football Leagues';

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
        $url = 'http://api.football-api.com/2.0/competitions?';
        $json = $this->getJsonFromArray($url);
        $covers = [
            /*445 => "PremierLeague.png",
            455 => "LaLiga.png",
            452 => "bundesliga.png",
            450 => "Ligue_1_Logo.png",*/
            456 => "LegaSerieA.png",
        ];

        $compareApiParseId = [
            /*1204 => 445,
            1399 => 455,
            1229 => 452,
            1221 => 450,*/
            1269 => 456,
        ];

        $compareTeamsApiParseId = [
            /*9287 => 67,
            10061 => 524,
            10122 => 529,
            10085 => 527,
            10437 => 9,
            10677 => 12,
            10285 => 5,
            10552 => 721,
            10307 => 18,
            15934 => 558,
            15679 => 77,
            15999 => 560,
            15692 => 78,*/
        ];

        foreach ($json as $data) {
            if (!isset($compareApiParseId[$data->id])) {
                continue;
            }

            $this->log('Parse ' . $data->name . ' league');

            $model = League::query()->active()->where('league_parse_id', $compareApiParseId[$data->id])->first();
            if(!$model) {
                $this->log('Model not Found' . $data->id);
                die();
            }

            $model->parse_id_v2 = (int) $data->id;
            $model->caption = $data->name;
            $model->save();

            $teamsJson = $this->getJsonFromArray('http://api.football-api.com/2.0/standings/' . $data->id);
            foreach ($teamsJson as $teamData) {
                $this->log('Parse ' . $teamData->team_name . ' team');
                $query =  Team::query()
                    ->where('name', 'LIKE', '%' . $teamData->team_name . '%')
                    ->orWhere('parse_id_v2', $teamData->team_id);

                if(isset($compareTeamsApiParseId[$teamData->team_id])) {
                    $query = Team::query()->where('parse_id', $compareTeamsApiParseId[$teamData->team_id]);
                }
                $team = $query->first();
                if(!$team) {
                    $this->log('Team not Found ' . $teamData->team_id . ' with Name ' . $teamData->team_name);
                    continue;
                }

                $team->recent_form = $teamData->recent_form;
                $team->country = $teamData->country;
                $team->parse_id_v2 = (int) $teamData->team_id;
                $team->save();
                Player::query()
                    ->where('parse_id', 0)
                    ->where('team_id', $team->id)
                    ->delete();


                $playersJson = $this->getJsonFromArray('http://api.football-api.com/2.0/team/' . $teamData->team_id);

                if(!isset($playersJson->squad[0])) {
                    $this->log('Players is empty for team ' . $playersJson->venue_name);
                    die();
                }
                foreach ($playersJson->squad as $player) {
                    Player::query()->updateOrCreate(['parse_id' => $player->id], [
                        'team_id' => $team->id,
                        'name' => (string) $player->name,
                        'position' => (string) $player->position,
                        'jersey_number' => (int) $player->number,
                        'parse_id' => (int) $player->id,
                        'market_value' => "",
                        'nationality' => "",
                        'contract_until' => "",
                    ]);
                }

                sleep(0.5);
            }
            sleep(0.5);
        }
    }

    private function getJsonFromArray($url = '')
    {
        if(!strpos($url,'?')) {
            $url .= '?';
        }
        $response = $this->client->get($url . '&Authorization=565ec012251f932ea4000001968456b7c7904f3068e6eef6b4d30f5f');
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
