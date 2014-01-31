<?php

namespace Raspberry\Controller;

use Raspberry\Chart\Chart;
use Raspberry\Espeak\Espeak;
use Raspberry\Sensors\SensorBuilder;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\SensorValuesGateway;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class SensorsController implements ControllerProviderInterface {

	public function connect(Application $app) {
		$controllers = $app['controllers_factory'];

		$controllers->get('/', function(Application $app) {
			return $app->redirect('/sensors/0');
		});

		$controllers->get('/{active_sensor_ids}', function($active_sensor_ids, Request $request, Application $app) {
			$dic = $app['dic'];
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

			$from = $request->query->get('from');
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

			return $app['twig']->render('sensors.html.twig', [
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

		$controllers->get('/espeak/{id}', function($sensor_id, Application $app) {
			$dic = $app['dic'];

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

		return $controllers;
	}

}