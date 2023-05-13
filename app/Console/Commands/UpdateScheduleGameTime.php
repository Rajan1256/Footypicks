<?php

namespace App\Console\Commands;

use App\Models\Schedule;
use Illuminate\Console\Command;

class UpdateScheduleGameTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:gameTime';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update game time Football Schedule';


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
        $scheduleCollection = Schedule::query()->where('start_game_time', 0)->get();
        foreach ($scheduleCollection as $model) {

            $this->log('Schedule Update Game Time. With date' . $model->date);
            $model->start_game_time = $model->date;
            $model->save();

            $this->log('Schedule Update Game Time.' . $model->name . ' With id: '. $model->id);
        }
    }

    private function log($message)
    {
        print_r($message . "\n");
    }
}
