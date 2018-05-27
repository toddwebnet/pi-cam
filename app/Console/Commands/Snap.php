<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Snap extends Command
{
    protected $signature = 'snap';

    public function handle()
    {
        print "\n\n";
        /* @var $api BaseApi */
        $ts = time();
PRINT app_path();
       // exec("fswebcam -r 1280x720 {$ts}.jpg");
        print "\n\n";
    }
}
