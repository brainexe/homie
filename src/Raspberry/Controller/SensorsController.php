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
     * @param string $activeSensorIds
     * @return string
     * @Route("/sensors/load/{active_sensor_ids}")
     */
    public function indexSensor(Request $request, $activeSensorIds)
    {
        $session = $request->getSession();

        if (empty($activeSensorIds)) {
            $activeSensorIds = $session->get(self::SESSION_LAST_VIEW) ?: '0';
        }

        $available_sensor_ids = $this->gateway->getSensorIds();
        if (empty($activeSensorIds)) {
            $activeSensorIds = implode(':', $available_sensor_ids);
        }

        $from = $request->query->get('from');
        if ($from === null) {
            $from = Chart::DEFAULT_TIME;
        } else {
            $from = (int)$from;
        }

        $session->set(self::SESSION_LAST_VIEW, $activeSensorIds);
        $session->set(self::SESSION_LAST_TIMESPAN, $from);

        $activeSensorIds = array_map('intval', explode(':', $activeSensorIds));

        $sensorValues        = [];

        $sensorsRaw     = $this->gateway->getSensors();
        $sensorObjects = $this->builder->getSensors();

        foreach ($sensorsRaw as &$sensor) {
            $sensor_id = $sensor['id'];

            if (!empty($sensor['last_value'])) {
                $sensor_obj           = $sensorObjects[$sensor['type']];
                $sensor['espeak']     = (bool)$sensor_obj->getEspeakText($sensor['last_value']);
                $sensor['last_value'] = $sensor_obj->formatValue($sensor['last_value']);
            } else {
                $sensor['espeak'] = false;
            }

            if ($activeSensorIds && !in_array($sensor_id, $activeSensorIds)) {
                continue;
            }
            $sensorValues[$sensor_id] = $this->valuesGteway->getSensorValues($sensor_id, $from);
        }

        $json = $this->chart->formatJsonData($sensorsRaw, $sensorValues);

        return [
            'sensors' => $sensorsRaw,
            'active_sensor_ids' => $activeSensorIds,
            'json' => $json,
            'current_from' => $from,
            'available_sensors' => $sensorObjects,
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
        $sensorType = $request->request->get('type');
        $name        = $request->request->get('name');
        $description = $request->request->get('description');
        $pin         = $request->request->get('pin');
        $interval    = $request->request->getInt('interval');
        $node        = $request->request->getInt('node');

        // TODO sensor vo builder
        $sensorVo              = new SensorVO();
        $sensorVo->name        = $name;
        $sensorVo->type        = $sensorType;
        $sensorVo->description = $description;
        $sensorVo->pin         = $pin;
        $sensorVo->interval    = $interval;
        $sensorVo->node        = $node;

        $this->gateway->addSensor($sensorVo);

        return $sensorVo;
    }

    /**
     * @param Request $request
     * @param integer $sensorId
     * @return boolean
     * @Route("/sensors/espeak/{sensor_id}/", name="sensor.espeak", csrf=true)
     */
    public function espeak(Request $request, $sensorId)
    {
        $sensor     = $this->gateway->getSensor($sensorId);
        $sensorObj = $this->builder->build($sensor['type']);

        $text = $sensorObj->getEspeakText($sensor['last_value']);

        $espeakVo = new EspeakVO($text);
        $event     = new EspeakEvent($espeakVo);
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
