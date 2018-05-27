<?php
print "<pre>";
print_r(getFiles());
function getFiles()
{
    $path = __DIR__ . '/images';
    return runCmd("ls -1 {$path}/*.jpg | xargs -n 1 basename");

}


function runCmd($cmd)
{
    ob_start();
    exec("{$cmd}", $output, $result);
    // $op = ob_get_contents();
    ob_end_clean();
    return $output;
}
