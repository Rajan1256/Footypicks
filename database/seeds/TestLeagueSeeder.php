<?php

use Illuminate\Database\Seeder;

class TestLeagueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $isCreate = factory(App\Models\League::class)
            ->create()
            ->each(function ($u) {
                $u->teams()->saveMany(factory(App\Models\Team::class, 2)->make());
            });
        if(!$isCreate) {
            $this->errorLog('League not create');
        }
        $leagueModel = App\Models\League::query()
            ->with('teams')
            ->orderBy('id', 'desc')
            ->first();

        factory(App\Models\Schedule::class, 10)
            ->create([
                'team_home_id' => $leagueModel->teams[0]->id,
                'team_away_id' => $leagueModel->teams[1]->id,
                'league_id' => $leagueModel->id,
            ]);
        $this->log('Create a new test League ' . $leagueModel->caption);
    }


    private function log($message)
    {
        print_r($message . "\n");
    }


    private function errorLog($message)
    {
        $this->log($message);
        die();
    }
}
