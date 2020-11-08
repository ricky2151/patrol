<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands =
    [
        
        
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->call(function () {
            
        //     // $process = new Process(['python', 'storage/app/public/dbintegrate3.py']);
        //     // $process->run();

        //     // // executes after the command finishes
        //     // if (!$process->isSuccessful()) {
        //     //     throw new ProcessFailedException($process);
        //     // }


        //     //or
        //     shell_exec("python storage/app/public/dbintegrate3.py 2>&1");
        // })->daily();
        //$schedule->exec('python /home/oem/monitoringsystem/patrol/laravelweb_PSy/storage/app/public/python/dbintegrate3.py')->everyMinute();
        $schedule->exec('python ../laravelweb_PSy/python_script/dbintegrate3.py')->everyMinute();
        //$schedule->command('backup:run')->everyMinute();
        // $schedule->command('RealTimeShift:run')
        //          ->everyMinute()
        //          ->sendOutputTo('storage/logs/realtimeshiftlog.log');
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
