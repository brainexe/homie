<?php

use Raspberry\Chart\Chart;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\SensorValuesGateway;
use Slim\Slim;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/** @var ContainerBuilder $dic */
include '../src/bootstrap.php';

$loader = new Twig_Loader_Filesystem('../templates');
$twig = new Twig_Environment($loader, array(
	'cache' => '../cache/twig',
	'auto_reload' => true
));

$app = new Slim(array(
	'debug' => true
));

$app->error(function (\Exception $e) use ($app, $twig) {
	echo $twig->render('error.html.twig', array(
		'error_message'=> $e->getMessage()
	));
});

$app->get('/', function() use ($twig, $dic) {
	echo $twig->render('index.html.twig');
});

$app->get('/sensors/(:id)', function($single_sensor_id = null) use ($twig, $dic) {
	/** @var SensorGateway $sensor_gateway */
	/** @var SensorValuesGateway $sensor_values_gateway */
	$sensor_values_gateway = $dic->get('SensorValuesGateway');
	/** @var Chart $chart */
	$chart = $dic->get('Chart');
	$sensor_gateway = $dic->get('SensorGateway');
	$sensors = $sensor_gateway->getSensors();
	$sensor_values = [];

	foreach ($sensors as $sensor) {
		$sensor_id = $sensor['id'];

		if ($single_sensor_id && $sensor_id !== $single_sensor_id) {
			continue;
		}
		$sensor_values[$sensor_id] = $sensor_values_gateway->getSensorValues($sensor_id);
	}

	$json = $chart->formatJsonData($sensors, $sensor_values);

	echo $twig->render('sensors.html.twig', ['sensors' => $sensors, 'json' => $json]);
});

$app->run();
