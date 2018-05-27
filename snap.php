<?php

// imagemagick
// [ "$( compare -metric rmse zelda3.jpg zelda3.jpg null: 2>&1 )" = "0 (0)" ] && echo "same" || echo "not same"

$dir = realpath(__DIR__ . '/public/images/');
for ($x = 0; $x < 10; $x++) {
    $ts = time();
    exec("fswebcam --no-banner -r 640 {$dir}/{$ts}.jpg");
    print "\n\n" . time() - $ts . "\n\n";
}


