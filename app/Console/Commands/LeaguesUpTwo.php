<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\League;
use App\Models\Schedule;
use App\Models\Goal;
use App\Models\Team;
use GuzzleHttp\Client;
use DateTime;

class LeaguesUpTwo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'league:two';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Old live league score update';

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
        $model = League::all();

        foreach ($model as $value) {


            if($value->current_matchday != null)
            {
                $getApiData = $this->runApiLeague($value->id);
                $matchDayInt = $getApiData->data->schedule[0]->matchday;

                $url = "http://api.football-data.org/v2/competitions/".$value->parse_id_v2."/matches?matchday=".$matchDayInt;
                $sc = $this->getJsonFromArray($url);
                foreach ($sc->matches as $el)
                {
                    $sc_data = Schedule::where('sch_id',$el->id)->get();

                    foreach ($sc_data as $live_sch)
                    {
                        if(empty($el->goals))
                        {
                            echo date("d-m-y H:i:s a");
                            echo 'No goals for update';
                        }
                        else
                        {
                            $finalData = "\n".$value->name."\n"."Current Match Day==".$value->current_matchday."\n".json_encode($el->goals)."\n\n";
                            $datetime = new DateTime();
                            $logEntry = $datetime->format('Y-m-d H:i:s A');
                            error_log("[".$logEntry."]".$finalData,3, storage_path('logs/liveScoreFinished.log'));

                            foreach ($el->goals as $rw)
                            {
                                if ($rw->team->id == $live_sch->team_home_id_parse)
                                {
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

                                }
                                if ($rw->team->id == $live_sch->team_away_id_parse)
                                {
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
                                }
                            }
                        }
                    }
                }
            }
            else
            {
                $getApiData = $this->runApiLeague($value->id);
                $stageName = $getApiData->data->schedule[0]->schedule_stage;

                $url = "http://api.football-data.org/v2/competitions/".$value->parse_id_v2."/matches?stage=".$stageName."&status=FINISHED";
                $sc = $this->getJsonFromArray($url);
                foreach ($sc->matches as $el)
                {
                    $sc_data = Schedule::where('sch_id',$el->id)->get();

                    foreach ($sc_data as $live_sch)
                    {
                        if(empty($el->goals))
                        {
                            echo date("d-m-y H:i:s a");
                            echo 'No goals for update';
                        }
                        else
                        {
                            $finalData = "\n".$value->name."\n"."Current Stage Name==".$stageName."\n".json_encode($el->goals)."\n\n";
                            $datetime = new DateTime();
                            $logEntry = $datetime->format('Y-m-d H:i:s A');
                            error_log("[".$logEntry."]".$finalData,3, storage_path('logs/liveScoreFinished.log'));

                            foreach ($el->goals as $rw)
                            {
                                if ($rw->team->id == $live_sch->team_home_id_parse)
                                {
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

                                }
                                if ($rw->team->id == $live_sch->team_away_id_parse)
                                {
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
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function runApiLeague($league_id)
    {
        $url = "http://18.195.230.229/api/league/".$league_id."/-1/leaguePaginate";

        $ch = curl_init($url);  
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'X-Api-Token: aa60ab4764e824049b8f8bceb53de04821ff3f80f3c85d85606412e6dfed9c0b0309341b1c219293'
        ));

        $resultJSON = curl_exec($ch);

        return json_decode($resultJSON);
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
