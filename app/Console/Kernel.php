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
        

        Commands\SyncUnits::class,  
        Commands\SyncClasses::class,  

        Commands\SyncDepartments::class, 



        Commands\SyncStaff::class,    
        Commands\SyncStudents::class,  

        Commands\SyncUsers::class, 

        
        
        Commands\SyncGroups::class,

        
        
       
        
       
       
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('sync:units')->dailyAt('00:00'); //與學校單位資料同步 
        $schedule->command('sync:classes')->dailyAt('00:15'); //與學校班級資料同步  

        $schedule->command('sync:departments')->dailyAt('00:30'); //與Teamplus部門資料同步

        
        $schedule->command('sync:staff')->dailyAt('01:00'); //與學校教職員資料同步
        $schedule->command('sync:students')->dailyAt('01:30'); //與學校學生資料同步 

        $schedule->command('sync:users')->dailyAt('02:00'); //與Teamplus使用者資料同步 

        
        $schedule->command('sync:groups')->dailyAt('06:00');
        
             
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
