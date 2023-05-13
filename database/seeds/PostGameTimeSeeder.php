<?php

use Illuminate\Database\Seeder;

class PostGameTimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $scheduleCollection = \App\Models\Schedule::query()->where('start_game_time', 0)->get();
        foreach ($scheduleCollection as $model) {
            $model->start_game_time = strtotime('date');
            $model->save();
        }
    }
}
