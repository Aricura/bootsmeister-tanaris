<?php

namespace App\Console;

use App\Console\Commands\GuildUpdater;
use App\Console\Commands\MemberAdd;
use App\Console\Commands\MemberRemove;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Artisan command kernel.
 *
 * @author Stefan Herndler
 * @since 1.0.0
 * @class Kernel
 * @package App\Console
 */
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        GuildUpdater::class,
		MemberAdd::class,
		MemberRemove::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule) {
		$schedule->command('guild:updater')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands() {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
