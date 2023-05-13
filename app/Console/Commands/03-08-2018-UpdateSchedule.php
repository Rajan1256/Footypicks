<?php

namespace App\Console\Commands;

use App\Models\Schedule;
use App\Models\Team;
use Football;
use App\Models\League;
use Illuminate\Console\Command;
use App\Services\Schedule as Service;

class UpdateSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:sc:u';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Football Schedule';


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
        $collections = League::where('status', League::ACTIVE)->get();
        foreach ($collections as $model) {
            $this->runParseModel($model);
        }
    }

    public function runParseModel(League $model){
        $this->log('Start Update ' . $model->name . ' With id: '. $model->id);
        if(!$model->league_parse_id) {
            $this->log('Error. No parseId. Parsed ' . $model->name . ' With id: '. $model->id);
            return;
        }

        $fixtures = Football::getLeagueFixtures($model->league_parse_id);

        if(!$fixtures->count) {
            $this->log('Fixtures count is empty. Team Name ' . $model->name . ' With id: '. $model->id);
            return;
        }

        $args = [];
        foreach ($fixtures->fixtures as $fixture) {

            $matchesHome = [];
            preg_match("/\d*$/", $fixture->_links->homeTeam->href, $matchesHome);
            $homeModel = Team::query()->where('parse_id', (int) $matchesHome[0])->first();

            $matches = [];
            preg_match("/\d*$/", $fixture->_links->awayTeam->href, $matches);
            $awayModel = Team::query()->where('parse_id', (int) $matches[0])->first();

            if($homeModel && $awayModel) {

            $dataSave = [
                'team_home_id_parse' => (int) $matchesHome[0],
                'team_away_id_parse' => (int) $matches[0],
                'date' => (string) $fixture->date,
                'matchday' => (int) $fixture->matchday,
                'goals_home_team' => (int) $fixture->result->goalsHomeTeam,
                'goals_away_team' => (int) $fixture->result->goalsAwayTeam,
                'league_id' => (int)$model->id,
                'start_game_time' => strtotime($fixture->date),
                'status' => Schedule::prepareStatus((string) $fixture->status),
            ];

            if(!$homeModel->id || !$awayModel->id) {
                $this->log('Fixtures not found Models' . " id home Team= $matchesHome[0] id away = $matches[0]");
            }


            $service = new Service();
            $service->setWhere([
                'team_home_id_parse' => $dataSave['team_home_id_parse'],
                'team_away_id_parse' => $dataSave['team_away_id_parse']
            ]);

            $service->getOne();
            if($service->getModel()) {
                if ($dataSave['status'] == Schedule::FINISHED) {
                    $this->log('Update Results ' . $service->getModel()->id . ' Schedule');
                   $service->scheduleFinish($dataSave);
                }
            }

            
            $dataSave['team_home_id'] = $homeModel->id;
            $dataSave['team_away_id'] = $awayModel->id;
            Schedule::query()->updateOrCreate([
                'team_home_id_parse' => $dataSave['team_home_id_parse'],
                'team_away_id_parse' => $dataSave['team_away_id_parse']
            ], $dataSave);
            if(isset($dataSave['date'], $service->getModel()->id))
            $this->log($service->getModel()->id . "  " . $dataSave['date']);
        }
    }
        $this->log('Fixtures Update.' . $model->name . ' With id: '. $model->id);
        sleep(2);
    }

    private function log($message)
    {
        print_r($message . "\n");
    }
}
