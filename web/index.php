<?php

use Raspberry\Twig\Extensions\SensorExtension;
use Silex\Application;
use Symfony\Component\DependencyInjection\Container;

/** @var Container $dic */
$dic = include '../src/bootstrap.php';

/** @var Application $app */
$app = $dic->get('Application');

$app['twig']->addExtension(new SensorExtension());
$app->error(function(Exception $e) use ($app) {
	return $app['twig']->render('error.html.twig', [
		'error_message'=> $e->getMessage()
	]);
});

$app->run();
