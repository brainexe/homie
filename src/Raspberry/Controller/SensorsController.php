<?php

namespace Raspberry\Controller;

use Matze\Core\Controller\AbstractController;
use Matze\Core\EventDispatcher\MessageQueueEvent;
use Matze\Core\Traits\EventDispatcherTrait;
use Raspberry\Chart\Chart;
use Raspberry\Sensors\SensorBuilder;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\SensorValuesGateway;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class SensorsController extends AbstractController {

	use EventDispatcherTrait;

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
	 * @var SensorBuilder;
	 */
	private $_sensor_builder;

	/**
	 * @var Chart
	 */
	private $_chart;

	/**
	 * @Inject({"@SensorGateway", "@SensorValuesGateway", "@Chart", "@SensorBuilder"})
	 */
	public function __construct(SensorGateway $sensor_gateway, SensorValuesGateway $sensor_values_gateway, Chart $chart, SensorBuilder $sensor_builder) {
		$this->_sensor_gateway = $sensor_gateway;
		$this->_sensor_values_gateway = $sensor_values_gateway;
		$this->_chart = $chart;
		$this->_sensor_builder = $sensor_builder;
	}

	/**
	 * @return string
	 */
	public function getRoutes() {
		return [
			'sensor.index' => [
				'pattern' => '/sensors/',
				'defaults' => ['_controller' =>  'Sensors::index']
			],
			'sensor.indexSensor' => [
				'pattern' => '/sensors/{active_sensor_ids}',
				'defaults' => ['_controller' =>  'Sensors::indexSensor']
			],
			'sensor.set' => [
				'pattern' => '/sensors/set/{id}/{status}/{value}/',
				'defaults' => ['_controller' =>  'Sensors::setStats']
			],
			'sensor.espeak' => [
				'pattern' => '/sensors/espeak/{sensor_id}/',
				'defaults' => ['_controller' =>  'Sensors::espeak']
			]
		];
	}

	public function index(Request $request) {
		$last_page = $request->getSession()->get(self::SESSION_LAST_VIEW) ? : '0';

		return new RedirectResponse(sprintf('/sensors/%s', $last_page));
	}

	public function indexSensor(Request $request, $active_sensor_ids) {
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

		return $this->render('sensors.html.twig', ['sensors' => $sensors, 'active_sensor_ids' => $active_sensor_ids ? : $available_sensor_ids, 'json' => $json, 'current_from' => $from, 'from_intervals' => [0 => 'All', 3600 => 'Last Hour', 86400 => 'Last Day', 86400 * 7 => 'Last Week', 86400 * 30 => 'Last Month',]]);
	}

	/**
	 * @param integer $sensor_id
	 * @return RedirectResponse
	 */
	public function espeak($sensor_id) {
		$sensor = $this->_sensor_gateway->getSensor($sensor_id);
		$sensor_obj = $this->_sensor_builder->build($sensor['type']);

		$text = $sensor_obj->getEspeakText($sensor['last_value']);
$text = 'foo';

		$event = new MessageQueueEvent('Espeak', 'speak', [$text, 130, 70]);
		$this->getEventDispatcher()->dispatch(MessageQueueEvent::NAME, $event);

		return $text;
//		return new RedirectResponse(sprintf('/sensors/%s', $sensor_id));
	}

}