<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Football;
use App\Models\League;
use App\Models\Team;
use GuzzleHttp\Client;
use App\Services\Schedule as Service;


class OnlyTeamStage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'df:stage:tm1';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        //$data = League::all();
         $data = League::whereIn('parse_id_v2',[2146,2145])->get();
        foreach ($data as $model) {

           // $bs_url = 'http://api.football-data.org/v2/competitions/'.$model->parse_id_v2.'/standings';

            $bs_url = 'http://api.football-data.org/v2/competitions/'.$model->parse_id_v2.'/standings';
            // $bs_url = 'http://api.football-data.org/v2/competitions/'.$model->parse_id_v2.'/matches?matchday='.$model->current_matchday;
            $sc = $this->getJsonFromArray($bs_url);

            /* FOR show total count of matches with leages */


            /* Loop for get match data */
            $as = count($sc->standings);


           // $data=[];

            $tm = Team::all();
            foreach ($sc->standings as $el) {

                foreach ($el->table as $pl) {


        if($el->type=='TOTAL' && $pl->position>0)
                    {
                        foreach ($tm as $rw)
                        {

                                if ($rw->parse_id_v2 == $pl->team->id) {
                                    $temp1 = '';
                                    $temp2 = '';
                                    $temp3 = '';
                                    for ($i = 0; $i < $pl->won; $i++) {
                                        $temp1 .= 'W';
                                    }

                                    for ($s = 0; $s < $pl->lost; $s++) {
                                        $temp2 .= "L";
                                    }

                                    for ($x = 0; $x < $pl->draw; $x++) {
                                        $temp3 .= "D";
                                    }
                                    $team_form = $temp2 . $temp3 . $temp1;
                                    //$team_form = $temp1.$temp2;
                                    //$ar = array_push($data,$temp1,$temp2);

//                                    $mydata = [
//                                        'played_games' => $pl->playedGames,
//                                                'position' => $pl->position,
//                                                'points' => $pl->points,
//                                                'wins' => $pl->won,
//                                                'draws' => $pl->draw,
//                                                'losses' => $pl->lost,
//                                                'team_group' => $el->group,
//                                                'recentform' => $team_form,
//                                    ];

                                    if($rw->league_id==$model->id && $rw->parse_id_v2==$pl->team->id) {
                                        Team::query()->where('parse_id_v2',$rw->parse_id_v2)->where('league_id',$model->id)
                                            ->update([
                                            'played_games' => $pl->playedGames,
                                            'position' => $pl->position,
                                            'points' => $pl->points,
                                            'wins' => $pl->won,
                                            'draws' => $pl->draw,
                                            'losses' => $pl->lost,
                                            'team_group' => $el->group
                                            //'recentform' => $team_form
                        ]);
                                        //Team::query()->updateOrCreate(['parse_id_v2' => $rw->parse_id_v2], $mydata);
                                    }
                                    else
                                    {
                                            Team::query()
                                                ->updateOrCreate(['league_id'=>$model->id,'parse_id_v2' => $rw->parse_id_v2],
                                                    [
                                                'name'=>$pl->team->name,
                                                'played_games' => $pl->playedGames,
                                                'position' => $pl->position,
                                                'points' => $pl->points,
                                                'wins' => $pl->won,
                                                'draws' => $pl->draw,
                                                'losses' => $pl->lost,
                                                'parse_id_v2' => $rw->parse_id_v2,
                                                'league_id'=>$model->id,
                                                'team_group' => $el->group
                                               // 'recentform' => $team_form
                                            ]);
                                        }



//                                    else if($rw->league_id!=$model->id && $rw->parse_id_v2==$pl->team->id)
//                                    {
//                                        Team::query()->create([
//                                            'name'=>$pl->team->name,
//                                            'played_games' => $pl->playedGames,
//                                            'position' => $pl->position,
//                                            'points' => $pl->points,
//                                            'wins' => $pl->won,
//                                            'draws' => $pl->draw,
//                                            'losses' => $pl->lost,
//                                            'parse_id_v2' => $rw->parse_id_v2,
//                                            'league_id'=>$model->id,
//                                            'team_group' => $el->group,
//                                            'recentform' => $team_form,
//                                        ]);
////                                        Team::query()->updateOrCreate(['parse_id_v2' => $rw->parse_id_v2],[
////                                            'name'=>$pl->team->name,
////                                            'played_games' => $pl->playedGames,
////                                                'position' => $pl->position,
////                                                'points' => $pl->points,
////                                                'wins' => $pl->won,
////                                                'draws' => $pl->draw,
////                                                'losses' => $pl->lost,
////                                                'parse_id_v2' => $rw->parse_id_v2,
////                                                'league_id'=>$model->id,
////                                                'team_group' => $el->group,
////                                                'recentform' => $team_form,
////                                                ]);
//                                    }
//                                    else
//                                    {
//                                        Team::query()->create([
//                                            'name'=>$pl->team->name,
//                                            'played_games' => $pl->playedGames,
//                                            'position' => $pl->position,
//                                            'points' => $pl->points,
//                                            'wins' => $pl->won,
//                                            'draws' => $pl->draw,
//                                            'losses' => $pl->lost,
//                                            'parse_id_v2' => $rw->parse_id_v2,
//                                            'league_id'=>$model->id,
//                                            'team_group' => $el->group,
//                                            'recentform' => $team_form,
//                                        ]);
//                                    }
//                                    if ($el->group == null) {
//                                        Team::where('parse_id_v2', $rw->parse_id_v2)
//                                            ->update([
//                                                'played_games' => $pl->playedGames,
//                                                'position' => $pl->position,
//                                                'points' => $pl->points,
//                                                'wins' => $pl->won,
//                                                'draws' => $pl->draw,
//                                                'losses' => $pl->lost,
//                                                'team_group' => '',
//                                                'recentform' => $team_form,
//                                            ]);
//                                    } else if ($pl->playedGames > 0) {
//                                        Team::where('parse_id_v2', $rw->parse_id_v2)
//                                            ->update([
//                                                'played_games' => $pl->playedGames,
//                                                'position' => $pl->position,
//                                                'points' => $pl->points,
//                                                'wins' => $pl->won,
//                                                'draws' => $pl->draw,
//                                                'losses' => $pl->lost,
//                                                'team_group' => $el->group,
//                                                'recentform' => $team_form,
//                                            ]);
//                                    }


                                //}
                            }

                        }

                    }


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
