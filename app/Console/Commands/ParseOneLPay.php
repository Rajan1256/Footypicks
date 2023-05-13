<?php

namespace App\Console\Commands;

use App\Models\Player;
use App\Models\Schedule;
use Football;
use App\Models\League;
use App\Models\Team;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
// use App\Services\Schedule as Service;

class ParseOneLPay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse_v2:one:lg';

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
        $leagues = [

            2021 => "PremierLeague.png",
            2015 => "Ligue_1_Logo.png",
            2002=>  "bundesliga.png",
            2014=>  "LaLiga.png",
            2019=>  "LegaSerieA.png",
            2001=> "UEFA_Champions_League.png",
            2000=> "2018_FIFA_World_Cup.png",

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
                $tem = 'http://api.football-data.org/v2/competitions/'.$league_id.'/teams';
                $mth = 'http://api.football-data.org/v2/competitions/'.$league_id.'/matches';
            $teamsJson = $this->getJsonFromArray($tem);
            $gameJson = $this->getJsonFromArray($mth);


            $mydata = [
                'caption' => $data->name,
                'name' => $data->name,
                'teams_count' => $teamsJson->count,
                'games_count' => $gameJson->count,
                'current_matchday' => $data->currentSeason->currentMatchday,
                'matchdays_count' => 38,
                // 'league_parse_id' => ,
                'parse_id_v2' =>$league_id,
                'cover' =>  $leagues[$league_id],
                'last_updated' => $data->lastUpdated,
            ];

//           echo "<pre>";
//           echo print_r($mydata);
//           echo "</pre>";

             $model = League::query()->updateOrCreate(['parse_id_v2'=>$league_id],$mydata);

           // $model = League::where('parse_id_v2',$league_id)->first();


//
            /* For get match details from below api with current date and from +1 year date. */
            //$from = date('Y-m-d');
            //$to = date('Y-m-d', strtotime("+1 year"));
            //$bs_url = 'http://api.football-data.org/v2/competitions/'.$league_id.'/matches?matchday=1';
            $bs_url = 'http://api.football-data.org/v2/competitions/'.$league_id.'/matches?matchday='.$data->currentSeason->currentMatchday;
            $sc = $this->getJsonFromArray($bs_url);

            /* FOR show total count of matches with leages */


            /* Loop for get match data */

            foreach ($sc->matches as $el) {

               //print_r($el->matchday);
               //print_r($el);
                if($el->matchday == $model->current_matchday)
                    {


                            $homeModel = Team::query()->where('parse_id_v2', (int)$el->homeTeam->id)->first();
                            $awayModel = Team::query()->where('parse_id_v2', (int)$el->awayTeam->id)->first();


                            if (!$homeModel) {
                                $tm_ply = 'http://api.football-data.org/v2/teams/' . $el->homeTeam->id;
                                $playersJson = $this->getJsonFromArray($tm_ply);
                                if (!isset($playersJson->squad[0])) {
                                    $this->log('Players is empty for team ' . $playersJson->name);
                                    // die();
                                }

                                $team = Team::query()->updateOrCreate(['parse_id' => $el->homeTeam->id], [
                                    'name' => $playersJson->name,
                                    'cover' => $playersJson->crestUrl,
                                    'played_games' => 0,
                                    'position' => 0,
                                    'points' => 0,
                               'wins' => 0,
                               'draws' => 0,
                               'losses' => 0,
                                    'country' => $playersJson->area->name,
                                    'league_id' => $model->id,
                                    'parse_id_v2' => $el->homeTeam->id,
                                    'parse_id' => 0                                ]);

                                $player_data = Team::where('parse_id_v2',$playersJson->id)->get();
                                foreach ( $player_data as $dr)
                                {

                                    foreach ($playersJson->squad as $player) {
                                        Player::query()->updateOrCreate(['parse_id' => $player->id], [
                                            'team_id' => $dr->id,
                                            'name' => (string)$player->name,
                                            'position' => (string)$player->position,
                                            'jersey_number' => 0,
                                            'parse_id' => (int)$player->id,
                                            'market_value' => "",
                                            'contract_until' => "",
                                            'nationality'=>$player->nationality,
                                        ]);
                                    }
                                }



                            }



                        if (!$awayModel) {
                            $aw_ply = 'http://api.football-data.org/v2/teams/' . $el->awayTeam->id;
                            $playersJson = $this->getJsonFromArray($aw_ply);
                            if (!isset($playersJson->squad[0])) {
                                $this->log('Players is empty for team ' . $playersJson->name);
                                // die();
                            }

                            $team = Team::query()->updateOrCreate(['parse_id' => $el->awayTeam->id], [
                                 'name' => $playersJson->name,
                                'cover' => $playersJson->crestUrl,
                                'played_games' => 0,
                                    'position' => 0,
                                    'points' => 0,
                               'wins' => 0,
                               'draws' => 0,
                               'losses' => 0,
                                'country' => $playersJson->area->name,
                                'league_id' => $model->id,
                                'parse_id_v2' => $el->awayTeam->id,
                                'parse_id' => 0                            ]);

                            $player_data = Team::where('parse_id_v2',$playersJson->id)->get();
                            foreach ( $player_data as $dr)
                            {

                                foreach ($playersJson->squad as $player) {
                                    Player::query()->updateOrCreate(['parse_id' => $player->id], [
                                        'team_id' => $dr->id,
                                        'name' => (string)$player->name,
                                        'position' => (string)$player->position,
                                        'jersey_number' => 0,
                                        'parse_id' => (int)$player->id,
                                        'market_value' => "",
                                        'contract_until' => "",
                                        'nationality'=>$player->nationality,
                                    ]);
                                }
                            }



                        }

//                            $awayModel = Team::query()->where('parse_id_v2', (int)$el->visitorteam_id)->first();
//                            if (!$awayModel) {
//
//                                $playersJson = $this->getJsonFromArray('http://api.football-api.com/2.0/team/' . $el->visitorteam_id);
//                                if (!isset($playersJson->squad[0])) {
//                                    $this->log('Players is empty for team ' . $playersJson->venue_name);
//                                    // die();
//                                }
//
//                                $team = Team::query()->updateOrCreate(['parse_id' => $el->visitorteam_id], [
//                                    'name' => $playersJson->name,
//                                    'cover' => '',
//                                    // 'played_games' => $playersJson->playedGames,
//                                    // 'position' => $teamData->position,
//                                    // 'points' => $teamData->statistics->points,
//                                    'wins' => $playersJson->statistics[0]->wins,
//                                    'draws' => $playersJson->statistics[0]->draws,
//                                    'losses' => $playersJson->statistics[0]->losses,
//                                    'country' => $playersJson->country,
//                                    'league_id' => $model->id,
//                                    'parse_id_v2' => $el->visitorteam_id,
//                                    'parse_id' => $el->visitorteam_id
//                                ]);
//
//                                foreach ($playersJson->squad as $player) {
//                                    Player::query()->updateOrCreate(['parse_id' => $player->id], [
//                                        'team_id' => $team->id,
//                                        'name' => (string)$player->name,
//                                        'position' => (string)$player->position,
//                                        'jersey_number' => (int)$player->number,
//                                        'parse_id' => (int)$player->id,
//                                        'market_value' => "",
//                                        'nationality' => "",
//                                        'contract_until' => "",
//                                    ]);
//                                }
//                            }

//                            $homeModel = Team::query()->where('parse_id_v2', (int)$el->localteam_id)->first();
//
//                            $awayModel = Team::query()->where('parse_id_v2', (int)$el->visitorteam_id)->first();
//
//                            if (!isset($homeModel->id, $awayModel->id)) {
//                                $this->log('Fixtures not found Models' . " id home Team= $el->localteam_id id away = $el->visitorteam_id");
//                                // throw new \Exception('Fixtures not found Models ' . $el->id);
//                                continue;
//                            }
//                            $a = explode(".", $el->formatted_date);
//                            $data = $a[2] . '-' . $a[1] . '-' . $a[0] . 'T' . $el->time . 'Z';
//                            $dataSave = [
//                                'team_home_id' => (int)$homeModel->id,
//                                'team_home_id_parse' => (int)$homeModel->parse_id_v2,
//                                'team_away_id' => (int)$awayModel->id,
//                                'team_away_id_parse' => (int)$awayModel->parse_id_v2,
//                                'date' => (string)$data,
//                                'matchday' => (int)$el->week,
//                                'league_id' => (int)$model->id,
//                                'goals_home_team' => (int)$el->localteam_score,
//                                'goals_away_team' => (int)$el->visitorteam_score,
//                                'start_game_time' => strtotime($data)
//                            ];
//
//                            $service = Schedule::create($dataSave);

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

    private function log($message){

        print_r($message . "\n");
    }
}
