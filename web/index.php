<?php

use Matze\Core\Application\AppKernel;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/** @var Container $dic */
$dic = include '../src/bootstrap.php';

$request = Request::createFromGlobals();
$request->setSession(new Session());

/** @var AppKernel $kernel */
$kernel = $dic->get('AppKernel');
$kernel->handle($request);
