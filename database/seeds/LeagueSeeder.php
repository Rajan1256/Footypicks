<?php

use App\Models\League;
use App\Models\Team;
use App\Models\Player;
use GuzzleHttp\Client;
use Illuminate\Database\Seeder;

class LeagueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Schema::disableForeignKeyConstraints();
        Team::truncate();
        League::truncate();
        Player::truncate();
        \App\Models\Schedule::truncate();
        Schema::enableForeignKeyConstraints();
        $client = new Client();

        $url = 'http://api.football-data.org/v1/competitions/';
        $json = $this->getJsonFromArray($client, $url);
        $covers = [
            445 => "PremierLeague.png",
            455 => "LaLiga.png",
            452 => "bundesliga.png",
            450 => "Ligue_1_Logo.png",
        ];

        foreach ($json as $data) {
            if(!in_array($data->id, [445, 455, 452, 450], true)) {
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
                'cover' => isset($covers[$data->id]) ? $covers[$data->id] : "",
                'last_updated' => $data->lastUpdated,
            ];

            $model = League::create($dataSave);

            $teamsJson = $this->getJsonFromArray($client, $data->_links->leagueTable->href);
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
                    'parse_id' => (int) $matches[0]
                ];
            }
            $model->teams()->createMany($teamsCollection);
        }
        return;
        /**
         * 1 -> 62
         *
         *
         * Campeonato Brasileiro da Série A -> Чемпионат Бразилии по футболу — Серия A
         * Premier League 2017/18 -> Английская Премьер-лига (OK -> 1)
         * Championship 2017/18 -> Чемпионат Футбольной лиги Англии
         * League One 2017/18 -> Первая Футбольная лига Англии
         * League Two 2017/18 -> Вторая Футбольная лига Англии
         * Eredivisie 2017/18 -> Высший дивизион Нидерландов по футболу
         * Ligue 1 2017/18 -> Франция Лига 1 (OK -> 5)
         * Ligue 2 2017/18 -> Чемпионат Франции по футболу
         * Ligue 2 2017/18 -> Чемпионат Франции по футболу
         * 1. Bundesliga 2017/18 -> Чемпіонат Німеччини з футболу( OK - 3)
         * 2. Bundesliga 2017/18 -> Чемпіонат Німеччини з футболу
         * Primera Division 2017 -> La liga(Ok - 2)
        */
    }

    private function getJsonFromArray(Client $client, $url = '') {
        $header = array('headers' => array('X-Auth-Token' => 'f735cdcb9210478bbe6c8cc9e8941537'));
        $response = $client->get($url, $header);
        $json = json_decode($response->getBody()->getContents());
        if($response->getStatusCode() !== 200) {
            var_dump($json);
            die();
        }

        return $json;
    }
}
