<?php

use Raspberry\Controller\EspeakController;
use Raspberry\Controller\GpioController;
use Raspberry\Controller\IndexController;
use Raspberry\Controller\RadioController;
use Raspberry\Controller\SensorsController;
use Raspberry\Twig\Extensions\SensorExtension;
use Silex\Application;
use Silex\Provider\TwigServiceProvider;

/** @var Container $dic */
include '../src/bootstrap.php';

$app = new Application([
	'debug' => $dic->getParameter('debug'),
	'dic' => $dic
]);

$app->register(new TwigServiceProvider(), [
	'twig.path' => '../templates',
]);
$app['twig']->addExtension(new SensorExtension());

$app->error(function(Exception $e) use ($app) {
	return $app['twig']->render('error.html.twig', [
		'error_message'=> $e->getMessage()
	]);
});

$app->mount('/', new IndexController());
$app->mount('/gpio/', new GpioController());
$app->mount('/radio/', new RadioController());
$app->mount('/espeak/', new EspeakController());
$app->mount('/sensors/', new SensorsController());

$app->run();
