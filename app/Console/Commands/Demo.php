<?php

namespace App\Console\Commands;

use App\Models\Schedule;
use App\Models\Team;
use Football;
use App\Models\League;
use Illuminate\Console\Command;
use App\Services\Schedule as Service;

class Demo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:demo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Demo';


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
         $this->log('Cron Run SuccessFully');
    }

    private function log($message)
    {
        print_r($message . "\n");
    }
}
