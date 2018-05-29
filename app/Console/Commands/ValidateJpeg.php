<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ValidateJpeg extends Command
{
    protected $signature = "validate:jpeg";

    public function handle(){
        $folder = realpath(app_path() . '/../public/images/');

        foreach($this->getFiles($folder) as $file) {
            $cmd = "djpeg -fast -onepass {$file} > /dev/null";
            print $cmd . "\n";
             print json_encode($this->runCmd($cmd));
            print "\n\n";


        }
    }

    private function getFiles($folder){
        $files = [];
        foreach(scandir($folder) as $file){
            if(in_array($file, ['.','..'])){
                continue;
            }
            $path = $folder . '/' . $file;
            if(is_dir($path)){
                $files = array_merge($files, $this->getFiles($path));
            } else{
                $info = (object)pathinfo($file);
                if($info->extension == 'jpg'){
                    $files[] = $path;
                }
            }

        }
        return $files;
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
