<?php

namespace App\Console\Commands;

use App\Models\Schedule;
use App\Models\Team;
use Football;
use App\Models\League;
use Illuminate\Console\Command;

class ParseSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:sc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse Football Schedule';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        League::where('status', League::ACTIVE)->update(['status' => League::IN_PARSE]);
        $countAll = League::where('status', League::IN_PARSE)->count('id');
        // while(true) {
            $count = League::where('status', League::IN_PARSE)->count('id');
            if($count <= 0) {
                $this->log('Parsed ' . $countAll . ' League.');
                die();
            }
            $collections = League::where('status', League::IN_PARSE)->take(30)->get();
            foreach ($collections as $model) {
                if(in_array($model->league_parse_id,  [445, 455, 452, 450,467, 464])) {
                    $model->status = $this->runParseModel($model);
                    $model->save();
                }
            }

            // sleep(10);
        // }

    }

    public function runParseModel(League $model){
        $model->status = League::ACTIVE;
        if(!$model->league_parse_id) {
            $this->log('Error. No parseId. Parsed ' . $model->name . ' With id: '. $model->id);
            return League::NOT_ACTIVE;
        }

        $fixtures = Football::getLeagueFixtures($model->league_parse_id);

        if(!$fixtures->count) {
            $this->log('Fixtures count is empty. Team Name ' . $model->name . ' With id: '. $model->id);
            return League::ACTIVE;
        }

        $args = [];
        if(isset($fixtures->fixtures)) {
        foreach ($fixtures->fixtures as $fixture) {

            $matchesHome = [];
            preg_match("/\d*$/", $fixture->_links->homeTeam->href, $matchesHome);
            $homeModel = Team::query()->where('parse_id', (int) $matchesHome[0])->first();

            $matches = [];
            preg_match("/\d*$/", $fixture->_links->awayTeam->href, $matches);
            $awayModel = Team::query()->where('parse_id', (int) $matches[0])->first();

            
            $dataSave = [
                'team_home_id_parse' => (int) $matchesHome[0],
                'team_away_id_parse' => (int) $matches[0],
                'date' => (string) $fixture->date,
                'matchday' => (int) $fixture->matchday,
                'goals_home_team' => (int) $fixture->result->goalsHomeTeam,
                'goals_away_team' => (int) $fixture->result->goalsAwayTeam,
                'status' => Schedule::prepareStatus((string) $fixture->status),
            ];
            
            if($homeModel && $awayModel) {
                if(!$homeModel->id || !$awayModel->id) {
                    $this->log('Fixtures not found Models' . " id home Team= $matchesHome[0] id away = $matches[0]");
                }
                
                $dataSave['team_home_id'] = $homeModel->id;
                $dataSave['team_away_id'] = $awayModel->id;
                $args[] = $dataSave;
            }

        }
        $this->log('Fixtures OK.' . $model->name . ' With id: '. $model->id);
        $model->schedules()->createMany($args);
        // sleep(2);

        return League::ACTIVE;
    }
    }

    private function log($message){

        print_r($message . "\n");
    }
}
