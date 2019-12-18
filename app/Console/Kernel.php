<?php
namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {

    }

    public function commands()
    {
        $commanDirectories = glob(app_path('Console/Components/*'), GLOB_ONLYDIR);
        $commanDirectories[] = app_path('Console');

        collect($commanDirectories)->each(function (string $commandDirectory) {
            $this->load($commandDirectory);
        });
    }
}
