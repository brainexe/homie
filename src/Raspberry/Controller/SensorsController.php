<?php

namespace Raspberry\Controller;

use BrainExe\Core\Controller\ControllerInterface;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Raspberry\Espeak\EspeakEvent;
use Raspberry\Espeak\EspeakVO;
use Raspberry\Sensors\Chart;
use Raspberry\Sensors\SensorBuilder;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\SensorValuesGateway;
use Raspberry\Sensors\SensorVO;

use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class SensorsController implements ControllerInterface
{

    use EventDispatcherTrait;

    const SESSION_LAST_VIEW     = 'last_sensor_view';
    const SESSION_LAST_TIMESPAN = 'last_sensor_timespan';

    /**
     * @var SensorGateway
     */
    private $gateway;

    /**
     * @var SensorValuesGateway
     */
    private $valuesGteway;

    /**
     * @var SensorBuilder;
     */
    private $builder;

    /**
     * @var Chart
     */
    private $chart;

    /**
     * @Inject({"@SensorGateway", "@SensorValuesGateway", "@Chart", "@SensorBuilder"})
     * @param SensorGateway $gateway
     * @param SensorValuesGateway $valuesGateway
     * @param Chart $chart
     * @param SensorBuilder $builder
     */
    public function __construct(
        SensorGateway $gateway,
        SensorValuesGateway $valuesGateway,
        Chart $chart,
        SensorBuilder $builder
    ) {
        $this->gateway        = $gateway;
        $this->valuesGteway = $valuesGateway;
        $this->chart                 = $chart;
        $this->builder        = $builder;
    }

    /**
     * @param Request $request
     * @param string $active_sensor_ids
     * @return string
     * @Route("/sensors/load/{active_sensor_ids}")
     */
    public function indexSensor(Request $request, $active_sensor_ids)
    {
        $session = $request->getSession();

        if (empty($active_sensor_ids)) {
            $active_sensor_ids = $session->get(self::SESSION_LAST_VIEW) ?: '0';
        }

        $available_sensor_ids = $this->gateway->getSensorIds();
        if (empty($active_sensor_ids)) {
            $active_sensor_ids = implode(':', $available_sensor_ids);
        }

        $from = $request->query->get('from');
        if ($from === null) {
            $from = Chart::DEFAULT_TIME;
        } else {
            $from = (int)$from;
        }

        $session->set(self::SESSION_LAST_VIEW, $active_sensor_ids);
        $session->set(self::SESSION_LAST_TIMESPAN, $from);

        $active_sensor_ids = array_map('intval', explode(':', $active_sensor_ids));

        $sensor_values        = [];

        $sensors_raw     = $this->gateway->getSensors();
        $sensor_objects = $this->builder->getSensors();

        foreach ($sensors_raw as &$sensor) {
            $sensor_id = $sensor['id'];

            if (!empty($sensor['last_value'])) {
                $sensor_obj           = $sensor_objects[$sensor['type']];
                $sensor['espeak']     = (bool)$sensor_obj->getEspeakText($sensor['last_value']);
                $sensor['last_value'] = $sensor_obj->formatValue($sensor['last_value']);
            } else {
                $sensor['espeak'] = false;
            }

            if ($active_sensor_ids && !in_array($sensor_id, $active_sensor_ids)) {
                continue;
            }
            $sensor_values[$sensor_id] = $this->valuesGteway->getSensorValues($sensor_id, $from);
        }

        $json = $this->chart->formatJsonData($sensors_raw, $sensor_values);

        return [
        'sensors' => $sensors_raw,
        'active_sensor_ids' => $active_sensor_ids,
        'json' => $json,
        'current_from' => $from,
        'available_sensors' => $sensor_objects,
        'from_intervals' => [
        0 => 'All',
        3600 => 'Last Hour',
        86400 => 'Last Day',
        86400 * 7 => 'Last Week',
        86400 * 30 => 'Last Month'
        ]
        ];
    }

    /**
     * @param Request $request
     * @return SensorVO
     * @Route("/sensors/add/", name="sensors.add", methods="POST", csrf=true)
     */
    public function addSensor(Request $request)
    {
        $sensor_type = $request->request->get('type');
        $name        = $request->request->get('name');
        $description = $request->request->get('description');
        $pin         = $request->request->get('pin');
        $interval    = $request->request->getInt('interval');
        $node        = $request->request->getInt('node');

     // TODO sensor vo builder
        $sensor_vo              = new SensorVO();
        $sensor_vo->name        = $name;
        $sensor_vo->type        = $sensor_type;
        $sensor_vo->description = $description;
        $sensor_vo->pin         = $pin;
        $sensor_vo->interval    = $interval;
        $sensor_vo->node        = $node;

        $this->gateway->addSensor($sensor_vo);

        return $sensor_vo;
    }

    /**
     * @param Request $request
     * @param integer $sensor_id
     * @return boolean
     * @Route("/sensors/espeak/{sensor_id}/", name="sensor.espeak", csrf=true)
     */
    public function espeak(Request $request, $sensor_id)
    {
        $sensor     = $this->gateway->getSensor($sensor_id);
        $sensor_obj = $this->builder->build($sensor['type']);

        $text = $sensor_obj->getEspeakText($sensor['last_value']);

        $espeak_vo = new EspeakVO($text);
        $event     = new EspeakEvent($espeak_vo);
        $this->dispatchInBackground($event);

        return true;
    }

    /**
     * @Route("/sensors/slim/{sensor_id}/", name="sensor.slim")
     * @param Request $request
     * @param integer $sensor_id
     * @return array
     */
    public function slim(Request $request, $sensor_id)
    {
        $sensor     = $this->gateway->getSensor($sensor_id);
        $sensor_obj = $this->builder->build($sensor['type']);
        $formatted_value = $sensor_obj->getEspeakText($sensor['last_value']);

        return [
        'sensor' => $sensor,
        'sensor_value_formatted' => $formatted_value,
        'sensor_obj' => $sensor_obj,
        'refresh_interval' => 60
        ];
    }

    /**
     * @Route("/sensors/value/", name="sensor.value")
     * @param Request $request
     * @return array
     */
    public function getValue(Request $request)
    {
        $sensor_id       = $request->query->getInt('sensor_id');

        $sensor          = $this->gateway->getSensor($sensor_id);
        $sensor_obj      = $this->builder->build($sensor['type']);
        $formatted_value = $sensor_obj->getEspeakText($sensor['last_value']);

        return [
        'sensor' => $sensor,
        'sensor_value_formatted' => $formatted_value,
        'sensor_obj' => $sensor_obj,
        'refresh_interval' => 60
        ];
    }
}
