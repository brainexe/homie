#!/usr/bin/php
<?php

use Symfony\Component\Console\Application;

$dic = include __DIR__ . '/src/bootstrap.php';

/** @var Application $application */
$application = $dic->get('Console');
$application->run();