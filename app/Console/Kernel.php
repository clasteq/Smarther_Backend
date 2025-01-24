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
        /*Commands\sendAttendanceNotification::class,
        Commands\sendAttendanceSMSNotification::class,
        Commands\sendPostNotification::class,*/
        Commands\sendPostSMSNotification::class,
        //Commands\updateFeesValues::class,
    ];
    
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        /*$schedule->command('send:attendanceNotification')->everyMinute();
        $schedule->command('send:attendancesmsNotification')->everyMinute();
        $schedule->command('send:postNotification')->everyMinute();*/
        $schedule->command('send:postsmsNotification')->everyMinute(); 
        //$schedule->command('update:feesvalues')->everyMinute(); 
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
