#!/usr/bin/php
<?php
date_default_timezone_set('America/Chicago');

leaveIfAlreadyRunning();

$dir = realpath(__DIR__ . '/public/images/');
while (true) {
    $ts = time();
    $fileName = "{$ts}.jpg";
    $imagePath = "{$dir}/{$fileName}";
    runCmd("fswebcam --no-banner -r 640 {$imagePath}");
    runCmd("cp {$imagePath} /var/www/pi-cam-bb/storage/images/{$fileName}");
    runCmd("rm {$imagePath}");
}

/********************************************************************************************************/
/********************************************************************************************************/
/********************************************************************************************************/

function compareImagesAndDeleteIfDupe($new, $old)
{
    if ($old == "")
        return;

    $cmd = '[ "$( compare -metric rmse ' . $new . ' ' . $old . ' null: 2>&1 )" = "0 (0)" ] && echo "same" || echo "not same"';
    print_r(runCmd($cmd));
}

function runCmd($cmd)
{
    ob_start();
    $op = exec("{$cmd} 2> /dev/null", $output, $result);
    // $op = ob_get_contents();
    ob_end_clean();
    return $output;
}

function leaveIfAlreadyRunning()
{
    $myPid = getmypid();
    $cmdPattern = "/usr/bin/php /var/www/pi-cam/snap.php";
    $cmd = 'ps -ef | awk \'/snap.php/{print $2"@"$8" "$9}\'';
    foreach (runCmd($cmd) as $line) {
        $ar = explode("@", $line);
        if ($ar[1] == $cmdPattern && $ar[0] != $myPid) {
            print "leaving";
            exit();
        }
    }
}

/********************************************************************************************************/
/********************************************************************************************************/

/********************************************************************************************************/

class LastImagePath
{
    protected $lastImagePath;
    protected $filePath;

    public function __construct()
    {
        $this->filePath = __DIR__ . '/lastimage.txt';
        $this->setPathFromFile();
    }

    public function getLastImagePath()
    {

        return $this->lastImagePath;
    }

    public function setLastImagePath($imagePath)
    {
        $this->lastImagePath = $imagePath;
        file_put_contents($this->filePath, $imagePath);
    }

    private function setPathFromFile()
    {
        if (!file_exists($this->filePath)) {
            return null;
        }
        $this->lastImagePath = trim(file_get_contents($this->filePath));
    }

}

