<?php

namespace App\Console;

// use App\Console\Commands\Europa;
 use App\Console\Commands\Livescore;
// use App\Console\Commands\TBDUpdate;
 use App\Console\Commands\UpdateOneLPay;
//use App\Console\Commands\UpdatePT;
//use App\Console\Commands\OnlyLeague;
use App\Console\Commands\OnlyTeam;
use App\Console\Commands\OnlyTeam2;
use App\Console\Commands\UpdateResult;
 use App\Console\Commands\StatusSchedule;
//use App\Console\Commands\UpdateShirt;
use App\Console\Commands\OnlyTeamStage;
use App\Console\Commands\OnlyTeamStage2;
 use App\Console\Commands\CustomPlayer;
use App\Console\Commands\Recent_form_team;
use App\Console\Commands\LiveLeaguesUpdate;
use App\Console\Commands\LeaguesUpTwo;
//use App\Console\Commands\UpdatePT;


use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
         //  OnlyLeague::class,
        //   Europa::class,
         UpdateOneLPay::class,
         StatusSchedule::class,
        OnlyTeam::class,
        OnlyTeam2::class,
 	   UpdateResult::class,
	
	//UpdateShirt::class,
 	 OnlyTeamStage::class,
         OnlyTeamStage2::class,
        //CustomPlayer::class,
         Recent_form_team::class,
         Livescore::class,
         LiveLeaguesUpdate::class,
         LeaguesUpTwo::class,
        //UpdatePT::class,
        //TBDUpdate::class,
 //UpdatePT::class,

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */

    protected function schedule(Schedule $schedule)
    {
	//$schedule->command('df:pt')
          //  ->everyThirtyMinutes();

//        $schedule->command('parse:sc:u')
//            ->sendOutputTo(storage_path('logs/parse-sc.log'))
//            ->everyMinute();

//        $schedule->command('parse_v2:one:lg')
//            ->sendOutputTo(storage_path('logs/parse_v2-sc-one.log'))
 //           ->everyMinute();
	
	 // $schedule->command('df:ts')
  //           ->daily();

        // $schedule->command('df:tbd')
        //     ->everyTenMinutes();

        // $schedule->command('df:pt')
        //     ->daily();

         $schedule->command('df:fm')
             ->hourly();

        //$schedule->command('command:ply')
        //    ->daily();

        $schedule->command('live:sc')
            ->everyMinute();

//        $schedule->command('df:lg')
//            ->hourly();
        $schedule->command('df:tm')
           ->hourly();

         $schedule->command('df:tm2')
	        ->hourly();         
	  $schedule->command('df:stage:tm1')
            ->everyThirtyMinutes();
        $schedule->command('df:stage:tm2')
             ->everyTenMinutes();

	       $schedule->command('df:rs')
               ->hourly();
	
	 $schedule->command('df:st')
           ->everyThirtyMinutes();

  //           $schedule->command('df:er')
  //           ->everyThirtyMinutes();

        $schedule->command('parse_v2:one:lg:u')
            ->sendOutputTo(storage_path('logs/parse-lg.log'))
		        ->everyThirtyMinutes();           
//        $schedule->command('parse:lg:u')
//            ->sendOutputTo(storage_path('logs/parse-lg.log'))
//            ->everyMinute();
//
//        $schedule->command('parse_v2:lg:u')
//            ->sendOutputTo(storage_path('logs/v2_parse-lg.log'))
//            ->everyMinute();

//         $schedule->command('parse:pl:u')
//             ->sendOutputTo(storage_path('logs/parse-pl.log'))
//             ->daily();
        $schedule->command('liveleague:update')
            ->sendOutputTo(storage_path('logs/liveleagueupdate.log'))
            ->cron('15 * * * * *');

        $schedule->command('league:two')
            ->sendOutputTo(storage_path('logs/liveleagueupdateTwo.log'))
            ->cron('17 * * * * *');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
