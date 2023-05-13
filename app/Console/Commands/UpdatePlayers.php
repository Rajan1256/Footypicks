<?php

namespace App\Console\Commands;

use App\Models\Player;
use Football;
use App\Models\Team;
use Illuminate\Console\Command;

class UpdatePlayers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:pl:u';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Football Teams Players';

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
        $countAll = Team::where('status', Team::ACTIVE)->count('id');
        if($countAll <= 0) {
            $this->log('Not have any teams');
            die();
        }
        while(true) {
            $count = Team::where('status', Team::ACTIVE)->count('id');
            if($count <= 0) {
                $this->log('Parsed ' . $countAll . '/' . $countAll . ' teams.');
                die();
            }
            $collections = Team::where('status', Team::ACTIVE)->take(30)->get();
            foreach ($collections as $team) {
                $this->runParseTeam($team);
            }

            sleep(10);
        }

    }

    public function runParseTeam(Team $team){
        if(!$team->parse_id) {
            $this->log('Error. No parseId. Parsed ' . $team->name . ' With id: '. $team->id);
            return;
        }

        $teamParse = Football::getTeamPlayers($team->parse_id);
        if(!$teamParse->count) {
            $this->log('Players count is empty. Team Name ' . $team->name . ' With id: '. $team->id);
            return;
        }

        $players = [];
        foreach ($teamParse->players as $key => $player) {

            $model = Player::query()->where('name', $player->name)->first();
            if(!$model) {
                $this->log('Model not Found' . $model->id);
                die();
            }

            $players[] = [
                'name' => (string) $player->name,
                'position' => (string) $player->position,
                'jersey_number' => (int) $player->jerseyNumber,
                'date_birth' => (string) $player->dateOfBirth,
                'market_value' => (string) $player->marketValue,
                'nationality' => $player->nationality,
                'parse_id' =>$key,
                'contract_until' => (string) $player->contractUntil,
            ];
        }
        $this->log('Players OK.' . $team->name . ' With id: '. $team->id);
        sleep(2);
        $team->players()->createMany($players);

        return Team::ACTIVE;
    }

    private function log($message){
        print_r($message . "\n");
    }
}
