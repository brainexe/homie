<?php

use Homie\AppServer;
use Symfony\Component\DependencyInjection\Container;

/** @var Container $dic */
$dic = include __DIR__ . '/../src/bootstrap.php';

/** @var AppServer $appServer */
$appServer = $dic->get('AppServer');
$appServer->start();
