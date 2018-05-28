<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;

class TransferImages extends Command
{
    protected $signature = 'transfer:images';

    public function handle()
    {
        $this->leaveIfAlreadyRunning();
        while (true) {
            $files = $this->getFiles();
            foreach ($files as $file) {
                $this->moveFile($file);
            }
            usleep(500000);
        }
    }

    private function moveFile($file)
    {
        if (in_array($file, ['.', '..'])) {
            return;
        }
        $info = (object)pathinfo($file);
        if ($info->extension != 'jpg') {
            return;
        }

        $ts = $info->filename;
        if (!is_numeric($ts)) {
            return;
        }
        $sourcePath = realpath(app_path('../storage/images'));
        $targetPath = realpath(app_path('../public/images'));
        $date = Carbon::createFromTimestamp($ts);
        list($year, $month, $day) = explode('-', $date->toDateString());
        $targetPath .= '/' . $year;
        if (!file_exists($targetPath)) {
            mkdir($targetPath);
        }
        $targetPath .= '/' . $month;
        if (!file_exists($targetPath)) {
            mkdir($targetPath);
        }
        $targetPath .= '/' . $day;
        if (!file_exists($targetPath)) {
            mkdir($targetPath);
        }
        rename($sourcePath . '/' . $info->basename, $targetPath . '/' . $info->basename);
    }

    private function getFiles()
    {
        return scandir(app_path('../storage/images'));
    }

    private function leaveIfAlreadyRunning()
    {
        $myPid = getmypid();
        $cmdPattern = "php /var/www/pi-cam/artisan transfer:images";
        $cmd = 'ps -ef | awk \'/artisan/{print $2"@"$8" "$9" "$10}\'';
        foreach ($this->runCmd($cmd) as $line) {
            $ar = explode("@", $line);
            if ($ar[1] == $cmdPattern && $ar[0] != $myPid) {
                print "leaving\n";
                exit();
            }
        }
    }

    private function runCmd($cmd)
    {
        ob_start();
        exec("{$cmd}", $output, $result);
        // $op = ob_get_contents();
        ob_end_clean();
        return $output;
    }

}
