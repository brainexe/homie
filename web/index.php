<?php

use Slim\Slim;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/** @var ContainerBuilder $dic */
include '../src/bootstrap.php';

$loader = new Twig_Loader_Filesystem('../templates');
$twig = new Twig_Environment($loader, array(
	'cache' => '../cache/twig',
));

$app = new Slim(array(
	'debug' => true
));

$app->error(function (\Exception $e) use ($app, $twig) {
	echo $twig->render('error.html.twig', array(
		'error_message'=> $e->getMessage()
	));
});

$pdo= $dic->get('PDO');
print_r($pdo);
$app->get('/', function() use ($twig, $dic) {
	echo $twig->render('index.html.twig');
});

$app->get('/sensors/', function() use ($twig, $dic) {
	echo $twig->render('sensors.html.twig');
});

$app->run();
