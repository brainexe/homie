<?php

use Predis\Client;
use Raspberry\Chart\Chart;
use Raspberry\Espeak\Espeak;
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

	$from = $app->request()->params('from');
	if ($from === null) {
		$from = Chart::DEFAULT_TIME;
	} else {
		$from = (int)$from;
	}

	$available_sensor_ids = [];
	foreach ($sensors as &$sensor) {
		$sensor_id = $sensor['id'];
		$available_sensor_ids[] = $sensor_id;

		if (!empty($sensor['last_value'])) {
			$sensor_obj = $sensor_builder->build($sensor);
			$sensor['last_value'] = $sensor_obj->formatValue($sensor['last_value']);
			$sensor['espeak'] = (bool)$sensor_obj->getEspeakText($sensor['last_value']);
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
$app->get('/sensors/espeak/(:id)', function($sensor_id) use ($dic, $app) {
	/** @var SensorGateway $sensor_gateway */
	/** @var SensorBuilder $sensor_builder */
	/** @var Espeak $espeak */
	$espeak = $dic->get('Espeak');
	$sensor_gateway = $dic->get('SensorGateway');
	$sensor_builder = $dic->get('SensorBuilder');

	$sensor = $sensor_gateway->getSensor($sensor_id);
	$sensor_obj = $sensor_builder->build($sensor);

	$text = $sensor_obj->getEspeakText($sensor['last_value']);
	$espeak->speak($text, 130, 70);

	$app->redirect(sprintf('/sensors/%s', $sensor_id));
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

$app->get('/espeak/', function() use ($twig) {
	$speakers = Espeak::getSpeakers();
	echo $twig->render('espeak.html.twig', ['speakers' => $speakers]);
});

$app->post('/espeak/', function() use ($dic, $app) {
	/** @var Espeak $espeak */
	$espeak = $dic->get('Espeak');

	$speaker = $app->request()->post('speaker');
	$espeak->speak($app->request()->post('text'), $app->request()->post('volume'), $app->request()->post('speed'), $speaker);

	$app->redirect('/espeak/');
});

$app->run();
