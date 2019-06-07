<?php

use App\Application;

$loader = require __DIR__.'/../vendor/autoload.php';

$application = new Application();
$application->run();
