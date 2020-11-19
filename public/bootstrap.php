<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('America/Toronto');

$starttime = explode(' ', microtime());
$starttime = $starttime[1] + $starttime[0];

require '../app/libraries/framework.php';
$f = new Framework();
require $f->get_controller_file_path();

$totaltime = explode(' ', microtime());
$totaltime = $totaltime[0] + $totaltime[1] - $starttime;
echo "\n\n".'<!-- '.round($totaltime, 4).' -->';
