<?php

use Raspberry\Chart\Chart;
use Raspberry\Gpio\GpioManager;
use Raspberry\Radio\RadioGateway;
use Raspberry\Radio\Radios;
use Raspberry\Sensors\SensorBuilder;
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

$app->get('/', function() use ($app) {
	$app->redirect('/sensors/');
});

$app->get('/sensors/(:id)', function($single_sensor_id = null) use ($twig, $dic) {
	/** @var SensorGateway $sensor_gateway */
	/** @var SensorValuesGateway $sensor_values_gateway */
	/** @var Chart $chart */
	/** @var SensorBuilder $sensor_builder */

	$sensor_builder = $dic->get('SensorBuilder');
	$sensor_values_gateway = $dic->get('SensorValuesGateway');
	$chart = $dic->get('Chart');
	$sensor_gateway = $dic->get('SensorGateway');

	$sensors = $sensor_gateway->getSensors();
	$latest_sensor_values = $sensor_values_gateway->getLatestValue();

	$sensor_values = [];

	foreach ($sensors as &$sensor) {
		$sensor_id = $sensor['id'];

		if (!empty($latest_sensor_values[$sensor_id])) {
			$sensor_obj = $sensor_builder->build($sensor);
			$sensor['latest'] = $sensor_obj->formatValue($latest_sensor_values[$sensor_id]);
		}

		if ($single_sensor_id && $sensor_id !== $single_sensor_id) {
			continue;
		}
		$sensor_values[$sensor_id] = $sensor_values_gateway->getSensorValues($sensor_id);
	}

	$json = $chart->formatJsonData($sensors, $sensor_values);

	echo $twig->render('sensors.html.twig', [
		'sensors' => $sensors,
		'single_sensor_id' => $single_sensor_id,
		'json' => $json
	]);
});

$app->get('/radio/', function() use ($twig, $dic) {
	/** @var Radios $radios */
	$radios = $dic->get('Radios');

	$radios_formatted = $radios->getRadios();

	echo $twig->render('radio.html.twig', ['radios' => $radios_formatted ]);
});

$app->get('/gpio/', function() use ($twig, $dic) {
	/** @var GpioManager $gpio_manager */
	$gpio_manager = $dic->get('GpioManager');

	$gpios = $gpio_manager->getPins();

	echo $twig->render('gpio.html.twig', ['gpio' => $gpios ]);
});

$app->run();
