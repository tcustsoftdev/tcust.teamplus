<?php

namespace App\Console;

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
        Commands\SyncDepartments::class, //OK

        Commands\SyncUnits::class,  //OK
        Commands\SyncClasses::class,  //OK
        
        Commands\SyncGroups::class, //OK

        Commands\SyncStaff::class,    //OK
        Commands\SyncStudents::class,  //OK
        
        Commands\SyncNotices::class,  //OK
        
       
       
    ];

    protected function schedule(Schedule $schedule)
    {
       
        $schedule->command('sync:notices')->everyFiveMinutes();
        
        $schedule->command('sync:classes')->dailyAt('00:00');
        $schedule->command('sync:units')->dailyAt('00:30');
        $schedule->command('sync:departments')->dailyAt('01:00');

        
        $schedule->command('sync:staff')->dailyAt('01:30');
        $schedule->command('sync:students')->dailyAt('02:00');

        
        $schedule->command('sync:group')->dailyAt('05:00');
        
             
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
