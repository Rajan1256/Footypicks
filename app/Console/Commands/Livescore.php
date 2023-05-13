<?php

namespace App\Console\Commands;
use App\Models\Schedule;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use App\Models\Goal;
use App\Models\Team;
use App\Models\Player;
class Livescore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'live:sc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Footypicks Live Score';
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
        $bs_url = 'http://api.football-data.org/v2/matches?status=LIVE';
        $sc = $this->getJsonFromArray($bs_url);


        if(empty($sc->matches))
        {
            echo 'No Live Match Now';
        }
        else
        {
            foreach ($sc->matches as $el)
            {
                $sc_data = Schedule::where('sch_id',$el->id)->get();
			
			 Schedule::where('sch_id', $el->id)
                                ->update([
                                	'status' => Schedule::prepareStatus((string)$el->status)
                                ]);

                foreach ($sc_data as $live_sch) {
                    if ($live_sch->sch_id == $el->id) {
                        
                            Schedule::where('sch_id', $el->id)
                                ->update([
                                    'goals_home_team' => $el->score->fullTime->homeTeam,
                                    'goals_away_team' => $el->score->fullTime->awayTeam,
                                    'status' => Schedule::prepareStatus((string)$el->status),
                                ]);
                        

                    }


                    foreach ($el->goals as $rw) {
                        if ($rw->team->id == $live_sch->team_home_id_parse) {
                            $tmModel = Team::query()->where('id', $live_sch->team_home_id)->where('parse_id_v2', $rw->team->id)->first();

                            if($rw->type=='PENALTY')
                            {
                                Goal::query()->updateOrCreate([
                                    'sch_id' => (int)$el->id,
                                    'team_id' => $tmModel->id,
                                    'player_id' => (int)$rw->scorer->id,
                                    'minute' => (int)$rw->minute,
                                    'status'=>'PK'
                                ]);
                            }
                            else if($rw->type=='OWN')
                            {
                                $tmModel5 = Team::query()->where('id', $live_sch->team_away_id)->first();
                                Goal::query()->updateOrCreate([
                                    'sch_id' => (int)$el->id,
                                    'team_id' => $tmModel5->id,
                                    'player_id' => (int)$rw->scorer->id,
                                    'minute' => (int)$rw->minute,
                                    'status'=>'OG'
                                ]);
                            }
                            else if($rw->type=='REGULAR')
                            {
                                Goal::query()->updateOrCreate([
                                    'sch_id' => (int)$el->id,
                                    'team_id' => $tmModel->id,
                                    'player_id' => (int)$rw->scorer->id,
                                    'minute' => (int)$rw->minute,
                                    'status'=>''
                                ]);
                            }

                            Player::query()->updateOrCreate(['parse_id' => $rw->scorer->id],
                                [
                                    'team_id' => $tmModel->id,
                                    'name' => $rw->scorer->name,
                                    'parse_id' => $rw->scorer->id,
                                    'nationality' => '',
                                    //'jersey_number'=>0
                                ]);
                        } else if ($rw->team->id == $live_sch->team_away_id_parse) {
                            $tmModel2 = Team::query()->where('id', $live_sch->team_away_id)->where('parse_id_v2', $rw->team->id)->first();


                            if($rw->type=='PENALTY')
                            {
                                Goal::query()->updateOrCreate([
                                    'sch_id' => (int)$el->id,
                                    'team_id' => $tmModel2->id,
                                    'player_id' => (int)$rw->scorer->id,
                                    'minute' => (int)$rw->minute,
                                    'status'=>'PK'
                                ]);
                            }
                            else if($rw->type=='OWN')
                            {
                                $tmModel6 = Team::query()->where('id', $live_sch->team_home_id)->first();
                                Goal::query()->updateOrCreate([
                                    'sch_id' => (int)$el->id,
                                    'team_id' => $tmModel6->id,
                                    'player_id' => (int)$rw->scorer->id,
                                    'minute' => (int)$rw->minute,
                                    'status'=>'OG'
                                ]);
                            }
                             else if($rw->type=='REGULAR')
                            {
                                Goal::query()->updateOrCreate([
                                    'sch_id' => (int)$el->id,
                                    'team_id' => $tmModel2->id,
                                    'player_id' => (int)$rw->scorer->id,
                                    'minute' => (int)$rw->minute,
                                    'status'=>''
                                ]);
                            }
                            Player::query()->updateOrCreate(['parse_id' => $rw->scorer->id],
                                [
                                    'team_id' => $tmModel2->id,
                                    'name' => $rw->scorer->name,
                                    'parse_id' => $rw->scorer->id,
                                    'nationality' => 'null',
                                    //'jersey_number'=>0
                                ]);
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
