<?php

namespace Raspberry\Controller;

use Matze\Core\Controller\AbstractController;
use Matze\Core\Traits\EventDispatcherTrait;
use Raspberry\Espeak\EspeakEvent;
use Raspberry\Espeak\EspeakVO;
use Raspberry\Sensors\Chart;
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
	const SESSION_LAST_TIMESPAN = 'last_sensor_timespan';

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
	 * @param Request $request
	 * @return RedirectResponse
	 * @Route("/sensors/", name="sensor.index")
	 */
	public function index(Request $request) {
		$session = $request->getSession();

		$last_page = $session->get(self::SESSION_LAST_VIEW) ?: '0';
		$last_page_timespan = $session->get(self::SESSION_LAST_TIMESPAN);

		if (null === $last_page_timespan) {
			$last_page_timespan = Chart::DEFAULT_TIME;
		}

		return new RedirectResponse(sprintf('/sensors/%s?from=%s', $last_page, $last_page_timespan));
	}

	/**
	 * @param Request $request
	 * @param string $active_sensor_ids
	 * @return string
	 * @Route("/sensors/{active_sensor_ids}")
	 */
	public function indexSensor(Request $request, $active_sensor_ids) {
		$from = $request->query->get('from');
		if ($from === null) {
			$from = Chart::DEFAULT_TIME;
		} else {
			$from = (int)$from;
		}

		$session = $request->getSession();

		$session->set(self::SESSION_LAST_VIEW, $active_sensor_ids);
		$session->set(self::SESSION_LAST_TIMESPAN, $from);

		$active_sensor_ids = explode(':', $active_sensor_ids);

		$sensors = $this->_sensor_gateway->getSensors();

		$sensor_values = [];
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

		return $this->render('sensors.html.twig', [
			'sensors' => $sensors,
			'active_sensor_ids' => $active_sensor_ids ? : $available_sensor_ids,
			'json' => $json,
			'current_from' => $from,
			'available_sensors' => $this->_sensor_builder->getSensors(),
			'from_intervals' => [
				0 => 'All',
				3600 => 'Last Hour',
				86400 => 'Last Day',
				86400 * 7 => 'Last Week',
				86400 * 30 => 'Last Month'
			]
		]);
	}

	/**
	 * @param Request $request
	 * @return RedirectResponse
	 * @Route("/sensors/add/", name="sensors.add", methods="POST")
	 */
	public function addSensor(Request $request) {
		$sensor_type = $request->request->get('type');
		$name = $request->request->get('name');
		$description = $request->request->get('description');
		$pin = $request->request->get('pin');
		$interval = $request->request->getInt('interval');

		$sensor = $this->_sensor_builder->build($sensor_type);

		$sensor_id = $this->_sensor_gateway->addSensor($name, $sensor->getSensorType(), $description, $pin, $interval);

		return new RedirectResponse(sprintf('/sensors/%d', $sensor_id));
	}

	/**
	 * @param integer $sensor_id
	 * @return RedirectResponse
	 * @Route("/sensors/espeak/{sensor_id}/", name="sensor.espeak")
	 */
	public function espeak($sensor_id) {
		$sensor = $this->_sensor_gateway->getSensor($sensor_id);
		$sensor_obj = $this->_sensor_builder->build($sensor['type']);

		$text = $sensor_obj->getEspeakText($sensor['last_value']);

		$espeak_vo = new EspeakVO($text);
		$event = new EspeakEvent($espeak_vo);
		$this->dispatchInBackground($event);

		return new RedirectResponse(sprintf('/sensors/%s', $sensor_id));
	}

	/**
	 * @Route("/sensors/slim/{sensor_id}/", name="sensor.slim")
	 * @param integer $sensor_id
	 * @return string
	 */
	public function slim($sensor_id) {
		$sensor = $this->_sensor_gateway->getSensor($sensor_id);
		$sensor_obj = $this->_sensor_builder->build($sensor['type']);

		return $this->render('sensor_slim.html.twig', [
			'sensor' => $sensor,
			'sensor_value_formatted' => $sensor_obj->getEspeakText($sensor['last_value']),
			'sensor_obj' => $sensor_obj,
			'refresh_interval' => 60
		]);
	}

}
