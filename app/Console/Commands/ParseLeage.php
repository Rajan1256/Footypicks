<?php

namespace App\Console\Commands;

use Football;
use App\Models\League;
use App\Models\Team;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class ParseLeage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:lg';

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
        $url = 'http://api.football-data.org/v1/competitions/';
        $json = $this->getJsonFromArray($url);
        $covers = [
            // 445 => "Ligue_1_Logo.png",
            // 455 => "PremierLeague.png",
            // 452 => "bundesliga.png",
            // 450 => "Ligue_1_Logo.png",
            // 456 => "Flamengo_logo.svg",
            467 => "2018_FIFA_World_Cup.png",
        ];

        foreach ($json as $data) {
            if (!in_array($data->id, [467 ], true)) {
                continue;
            }

            $dataSave = [
                'caption' => $data->caption,
                'name' => $data->league,
                'teams_count' => $data->numberOfTeams,
                'games_count' => $data->numberOfGames,
                'current_matchday' => $data->currentMatchday,
                'matchdays_count' => $data->numberOfMatchdays,
                'league_parse_id' => $data->id,
                'parse_id_v2' => 0,
                'cover' => isset($covers[$data->id]) ? $covers[$data->id] : "",
                'last_updated' => $data->lastUpdated,
            ];

            $model = League::create($dataSave);

            $teamsJson = $this->getJsonFromArray($data->_links->leagueTable->href);
            $teamsCollection = [];
            foreach ($teamsJson->standing as $teamData) {
                $matches = [];
                preg_match("/\d*$/", $teamData->_links->team->href, $matches);

                $teamsCollection[] = [
                    'name' => $teamData->teamName,
                    'cover' => isset($teamData->crestURI) ? $teamData->crestURI : '',
                    'played_games' => $teamData->playedGames,
                    'position' => $teamData->position,
                    'points' => $teamData->points,
                    'wins' => $teamData->wins,
                    'draws' => $teamData->draws,
                    'losses' => $teamData->losses,
                    'parse_id_v2' => 0,
                    'parse_id' => (int)$matches[0]
                ];
            }
            $model->teams()->createMany($teamsCollection);
        }
    }

    private function getJsonFromArray($url = '')
    {
        $header = array('headers' => array('X-Auth-Token' => 'f735cdcb9210478bbe6c8cc9e8941537'));
        $response = $this->client->get($url, $header);
        $json = json_decode($response->getBody()->getContents());
        if ($response->getStatusCode() !== 200) {
            var_dump($json);
            die();
        }

        return $json;
    }
}
