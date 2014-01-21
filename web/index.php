<?php

use Predis\Client;
use Raspberry\Chart\Chart;
use Raspberry\Gpio\GpioManager;
use Raspberry\Gpio\PinGateway;
use Raspberry\Radio\RadioController;
use Raspberry\Radio\Radios;
use Raspberry\Sensors\SensorBuilder;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\SensorValuesGateway;
use Raspberry\Twig\Extensions\SensorExtension;
use Slim\Slim;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/** @var ContainerBuilder $dic */
include '../src/bootstrap.php';

$loader = new Twig_Loader_Filesystem('../templates');
$twig = new Twig_Environment($loader, array(
	'cache' => '../cache/twig',
	'auto_reload' => true
));
$twig->addExtension(new Twig_Extension_Optimizer(Twig_NodeVisitor_Optimizer::OPTIMIZE_ALL));
$twig->addExtension(new SensorExtension());

$app = new Slim(array(
	'debug' => true
));

$app->error(function (\Exception $e) use ($app, $twig) {
	echo $twig->render('error.html.twig', array(
		'error_message'=> $e->getMessage()
	));
});

$app->get('/', function() use ($twig) {
	echo $twig->render('index.html.twig');
});

$app->get('/sensors/(:ids)', function($active_sensor_ids = '') use ($twig, $dic, $app) {
	$active_sensor_ids = array_filter(explode(':', $active_sensor_ids));

	/** @var SensorGateway $sensor_gateway */
	/** @var SensorValuesGateway $sensor_values_gateway */
	/** @var Chart $chart */
	/** @var SensorBuilder $sensor_builder */

	$sensor_builder = $dic->get('SensorBuilder');
	$sensor_values_gateway = $dic->get('SensorValuesGateway');
	$chart = $dic->get('Chart');
	$sensor_gateway = $dic->get('SensorGateway');

	$sensors = $sensor_gateway->getSensors();

	$sensor_values = [];

	$from = (int)$app->request()->params('from');

	$available_sensor_ids = [];
	foreach ($sensors as &$sensor) {
		$sensor_id = $sensor['id'];
		$available_sensor_ids[] = $sensor_id;

		if (!empty($sensor['last_value'])) {
			$sensor_obj = $sensor_builder->build($sensor);
			$sensor['last_value'] = $sensor_obj->formatValue($sensor['last_value']);
		}

		if ($active_sensor_ids && !in_array($sensor_id, $active_sensor_ids)) {
			continue;
		}
		$sensor_values[$sensor_id] = $sensor_values_gateway->getSensorValues($sensor_id, $from);
	}

	$json = $chart->formatJsonData($sensors, $sensor_values);

	echo $twig->render('sensors.html.twig', [
		'sensors' => $sensors,
		'active_sensor_ids' => $active_sensor_ids ?: $available_sensor_ids,
		'json' => $json,
		'current_from' => $from,
		'from_intervals' => [
			0 => 'All',
			3600 => 'Last Hour',
			86400 => 'Last Day',
			86400*7 => 'Last Week',
			86400*30 => 'Last Month',
		]
	]);
});

$app->get('/radio/', function() use ($twig, $dic) {
	/** @var Radios $radios */
	$radios = $dic->get('Radios');

	$radios_formatted = $radios->getRadios();

	echo $twig->render('radio.html.twig', ['radios' => $radios_formatted ]);
});

$app->get('/radio/:id/:status/', function($id, $status) use ($app, $dic) {
	/** @var Radios $radios */
	/** @var Client $predis */
	$predis = $dic->get('Predis');
	$radios = $dic->get('Radios');

	$radio = $radios->getRadios()[$id];
	$radio['status'] = $status;

	$predis->PUBLISH('radio_changes', serialize($radio));

	$app->redirect('/radio/');
});

$app->get('/gpio/', function() use ($twig, $dic) {
	/** @var GpioManager $gpio_manager */
	$gpio_manager = $dic->get('GpioManager');

	$pins = $gpio_manager->getPins();

	echo $twig->render('gpio.html.twig', ['pins' => $pins ]);
});

$app->get('/gpio/set/:id/:status/:value/', function($id, $status, $value) use ($twig, $dic, $app) {
	/** @var GpioManager $gpio_manager */
	$gpio_manager = $dic->get('GpioManager');

	$gpio_manager->setPin($id, $status, $value);

	$app->redirect('/gpio/');
});

$app->run();
