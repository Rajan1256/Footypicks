<?php

namespace App\Console\Commands;

use Football;
use App\Models\League;
use App\Models\Team;
use App\Models\Player;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class UpdateLeague extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:lg:u';

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
        $covers = [
            445 => "PremierLeague.png",
            455 => "LaLiga.png",
            452 => "bundesliga.png",
            450 => "Ligue_1_Logo.png",
            464 => "UEFA_Champions_League.png",
            467 => "2018_FIFA_World_Cup.png",
        ];

        foreach ([445,455, 452, 450 ] as $comp_id) {
            // if (!in_array($data->id, [445, 455, 452, 450,467], true)) {
            //     continue;
            // }
            
        $url = 'http://api.football-data.org/v1/competitions/'.$comp_id;
        $data = $this->getJsonFromArray($url);


            $dataSave = [
                'teams_count' => $data->numberOfTeams,
                'games_count' => $data->numberOfGames,
                'current_matchday' => $data->currentMatchday,
                'matchdays_count' => $data->numberOfMatchdays,
                'league_parse_id' => $data->id,
                'cover' => isset($covers[$data->id]) ? $covers[$data->id] : "",
                'last_updated' => $data->lastUpdated,
            ];

            $model = League::query()->where('league_parse_id', $data->id)->first();
            if(!$model) {
                $this->log('Model not Found' . $data->id);
                die();
            }
            $model->fill($dataSave);
            $model->save();

            $teamdataJson =  $this->getJsonFromArray($data->_links->teams->href); 
            
            if(isset($teamdataJson->teams)) {
                foreach ($teamdataJson->teams as $teamData) {
                    $matches = [];
                    preg_match("/\d*$/", $teamData->_links->self->href, $matches);
                    
                    Team::query()->updateOrCreate(['parse_id' => (int)$matches[0]], [
                        // 'name' => $teamData->teamName,
                        // 'cover' => isset($teamData->crestUrl) ? $teamData->crestUrl : '',
                        // 'played_games' => $teamData->playedGames,
                        // 'position' => $teamData->position,
                        // 'points' => $teamData->points,
                        // 'wins' => $teamData->wins,
                        // 'draws' => $teamData->draws,
                        // "league_id"=>   $model->id,
                        // 'losses' => $teamData->losses,
                        // 'parse_id' => (int)$matches[0]
                    ]);
                }
            }

            $teamsJson =  $this->getJsonFromArray($data->_links->leagueTable->href);
            
            


            if(isset($teamsJson->standing)) {
                foreach ($teamsJson->standing as $teamData) {
                    $matches = [];
                    preg_match("/\d*$/", $teamData->_links->team->href, $matches);

                    Team::query()->updateOrCreate(['parse_id' => (int)$matches[0]], [
                        'name' => $teamData->teamName,
                    //    'cover' => isset($teamData->crestURI) ? $teamData->crestURI : '',
                        'played_games' => $teamData->playedGames,
                        'position' => $teamData->position,
                        'points' => $teamData->points,
                        'wins' => $teamData->wins,
                        'draws' => $teamData->draws,
                        "league_id"=>   $model->id,
                        'losses' => $teamData->losses,
                        'parse_id' => (int)$matches[0]
                    ]);
                }
            }

            if(isset($teamsJson->standings)) {
                foreach ($teamsJson->standings as $teams) {

                    foreach ($teams as $teamData) {
                        $matches = [];
                        // preg_match("/\d*$/", $teamData->_links->team->href, $matches);

                        Team::query()->updateOrCreate(['parse_id' => (int)$teamData->teamId], [
                            'name' => $teamData->team,
                        //    'cover' => isset($teamData->crestURI) ? $teamData->crestURI : '',
                            'played_games' => $teamData->playedGames,
                            'position' => $teamData->position ?? 0,
                            'points' => $teamData->points,
                            'wins' => $teamData->wins?? 0,
                            'draws' => $teamData->draws?? 0,
                            'losses' => $teamData->losses?? 0,
                            "league_id"=>  $model->id,
                            'parse_id' => $teamData->teamId
                        ]);
                    }
                }
            }
        }

        
        foreach ([] as $comp_id) {
            
        // $url = 'http://api.football-data.org/v2/competitions/'.$comp_id;
        // $data = $this->getJsonFromArray($url);

        $url = 'http://api.football-data.org/v2/competitions/'.$comp_id ."/teams";
        $teams= $this->getJsonFromArray($url);
        $teamData= $teams->teams ;


        $url = 'http://api.football-data.org/v2/competitions/'.$comp_id ."/standings";
        $standings = $this->getJsonFromArray($url);

            $dataSave = [
                'teams_count' => count($teamData),
                // 'games_count' => $data->numberOfGames,
                'current_matchday' => $teams->season->currentMatchday ?? 0,
                // 'matchdays_count' => $data->numberOfMatchdays,
                // 'league_parse_id' => $data->id,
                // 'cover' => isset($covers[$data->id]) ? $covers[$data->id] : "",
                'last_updated' => $teams->competition->lastUpdated,
            ];

            $model = League::query()->where('parse_id_v2', $comp_id)->first();
            if(!$model) {
                $this->log('Model not Found' . $comp_id);
                die();
            }
            $model->fill($dataSave);
            $model->save();



            if(isset($standings->standings)) {
                foreach ($standings->standings as $eachdata) {
                    if($eachdata->type == "TOTAL") {

                        
                        foreach ($eachdata->table as $teamData) {

                        $url = 'http://api.football-data.org/v2/teams/'. $teamData->team->id ;
                        $players_data = $this->getJsonFromArray($url);
                        

                        $players = [];
                        foreach ($players_data->squad as $player) {
                            
                            
                            $players[] = [
                                'name' => (string) $player->name,
                                'position' => (string) $player->position,
                                'jersey_number' =>  $player->jerseyNumber ?? 0,
                                'date_birth' => (string) $player->dateOfBirth ?? "-",
                                // 'market_value' => (string) $player->marketValue ?? "-",
                                'nationality' => $player->nationality,
                                'parse_id' =>$player->id,
                                'team_id' => $teamData->team->id,
                                // 'contract_until' => (string) $player->contractUntil,
                            ];

                            
                        }


                        $team = Team::query()->updateOrCreate(['parse_id' => (int) $teamData->team->id ], [
                            'name' => $teamData->team->name,
                            // 'cover' => isset($teamData->crestURI) ? $teamData->crestURI : '',
                            'played_games' => $teamData->playedGames,
                            'position' => $teamData->position,
                            'points' => $teamData->points,
                            'wins' => $teamData->won,
                            'draws' => $teamData->draw,
                            'losses' => $teamData->lost,
                            "league_id"=>  $model->id,
                            'parse_id' =>  $teamData->team->id
                        ]);

                        $plIds=[];
                        $filteredPlayers =[];
                        foreach ($players as $pl) {
                            
                            $fTag = player::where( ["parse_id"=> $pl['parse_id']])->first();

                            if(!$fTag ) {
                                $filteredPlayers[] =  $pl;
                            }
                        
                        }

                        // dd( $filteredPlayers );
                        $team->players()->createMany($filteredPlayers);
                        $this->log('Players OK.' . $team->name . ' With id: '. $team->id);
                        sleep(8);

                        }
                    }
                }
            }
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

    private function log($message){

        print_r($message . "\n");
    }
}
