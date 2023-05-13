<?php

namespace App\Console\Commands;

use Football;
use App\Models\Team;
use Illuminate\Console\Command;

class ParsePlayers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:pl';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse Football Teams Players';

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
        Team::where('status', Team::ACTIVE)->update(['status' => Team::IN_PARSE]);
        $countAll = Team::where('status', Team::IN_PARSE)->count('id');
        while(true) {
            $count = Team::where('status', Team::IN_PARSE)->count('id');
            if($count <= 0) {
                $this->log('Parsed ' . $countAll . ' teams.');
                die();
            }
            $collections = Team::where('status', Team::IN_PARSE)->take(30)->get();
            foreach ($collections as $team) {
                $team->status = $this->runParseTeam($team);
                $team->save();
            }

            sleep(10);
        }

    }

    public function runParseTeam(Team $team){
        if(!$team->parse_id) {
            $team->status =
            $this->log('Error. No parseId. Parsed ' . $team->name . ' With id: '. $team->id);
            return Team::NOT_ACTIVE;
        }

        $teamParse = Football::getTeamPlayers($team->parse_id);
        if(!$teamParse->count) {
            $this->log('Players count is empty. Team Name ' . $team->name . ' With id: '. $team->id);
            return Team::ACTIVE;
        }

        $players = [];
        foreach ($teamParse->players as $key => $player) {
            // dd($player);
            $players[] = [
                'name' => (string) $player->name,
                'position' => (string) $player->position ?? "",
                'jersey_number' => (int) $player->jerseyNumber ?? 0,
                'date_birth' => (string) $player->dateOfBirth ?? "",
                'market_value' => (string) $player->marketValue ?? "",
                'nationality' => $player->nationality ?? "",
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
