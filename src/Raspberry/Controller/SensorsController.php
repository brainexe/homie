<?php

namespace Raspberry\Controller;

use Raspberry\Chart\Chart;
use Raspberry\Espeak\Espeak;
use Raspberry\Sensors\SensorBuilder;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\SensorValuesGateway;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations as DI;

/**
 * @DI\Service(name="Controller.SensorsController", public=false, tags={{"name" = "controller"}})
 */
class SensorsController implements ControllerInterface {

	const SESSION_LAST_VIEW = 'last_sensor_view';

	/**
	 * @var SensorGateway
	 */
	private $_sensor_gateway;

	/**
	 * @var SensorValuesGateway
	 */
	private $_sensor_values_gateway;

	/**
	 * @var Espeak
	 */
	private $_espeak;

	/**
	 * @var SensorBuilder;
	 */
	private $_sensor_builder;

	/**
	 * @var Chart
	 */
	private $_chart;

	/**
	 * @DI\Inject({"@SensorGateway", "@SensorValuesGateway", "@Chart", "@SensorBuilder", "@Espeak"})
	 */
	public function __construct(SensorGateway $sensor_gateway, SensorValuesGateway $sensor_values_gateway, Chart $chart, SensorBuilder $sensor_builder, Espeak $espeak) {
		$this->_sensor_gateway = $sensor_gateway;
		$this->_sensor_values_gateway = $sensor_values_gateway;
		$this->_espeak = $espeak;
		$this->_chart = $chart;
		$this->_sensor_builder = $sensor_builder;
	}

	/**
	 * @return string
	 */
	public function getPath() {
		return '/sensors/';
	}

	public function connect(Application $app) {
		$controllers = $app['controllers_factory'];

		$controllers->get('/', function(Request $request, Application $app) {
			$last_page = $request->getSession()->get(self::SESSION_LAST_VIEW) ?: '0';

			return $app->redirect(sprintf('/sensors/%s', $last_page));
		});

		$controllers->get('/{active_sensor_ids}', function($active_sensor_ids, Request $request, Application $app) {
			$request->getSession()->set(self::SESSION_LAST_VIEW, $active_sensor_ids);

			$active_sensor_ids = explode(':', $active_sensor_ids);

			$sensors = $this->_sensor_gateway->getSensors();

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
					$sensor_obj = $this->_sensor_builder->build($sensor['type']);
					$sensor['last_value'] = $sensor_obj->formatValue($sensor['last_value']);
					$sensor['espeak'] = (bool)$sensor_obj->getEspeakText($sensor['last_value']);
				} else {
					$sensor['espeak'] = false;
				}

				if ($active_sensor_ids && !in_array($sensor_id, $active_sensor_ids)) {
					continue;
				}
				$sensor_values[$sensor_id] = $this->_sensor_values_gateway->getSensorValues($sensor_id, $from);
			}

			$json = $this->_chart->formatJsonData($sensors, $sensor_values);

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
			$sensor = $this->_sensor_gateway->getSensor($sensor_id);
			$sensor_obj = $this->_sensor_builder->build($sensor['type']);

			$text = $sensor_obj->getEspeakText($sensor['last_value']);
			$this->_espeak->speak($text, 130, 70);

			$app->redirect(sprintf('/sensors/%s', $sensor_id));
		});

		return $controllers;
	}

}