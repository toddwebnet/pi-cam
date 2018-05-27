<?php

$files = scandir(__DIR__);
foreach($files as $file)
{
    if(!in_array($file, ['.','..'])){
        print "<a href=\"{$file}\" target=\"_blank\">{$file}</a><BR>";
    }

}
