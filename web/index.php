<?php

use Matze\Core\Application\AppKernel;
use Matze\Core\Application\Bootstrap;
use Raspberry\Twig\Extensions\SensorExtension;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/** @var Container $dic */
$dic = include '../src/bootstrap.php';

$request = Request::createFromGlobals();
$request->setSession(new Session());

$kernel = new AppKernel($request, $dic);
$kernel->handle();