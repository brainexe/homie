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

use Raspberry\Sensors\SensorVOBuilder;
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
    private $valuesGateway;

    /**
     * @var SensorVOBuilder
     */
    private $voBuilder;

    /**
     * @var SensorBuilder;
     */
    private $builder;

    /**
     * @var Chart
     */
    private $chart;

    /**
     * @Inject({"@SensorGateway", "@SensorValuesGateway", "@Chart", "@SensorBuilder", "@SensorVOBuilder"})
     * @param SensorGateway $gateway
     * @param SensorValuesGateway $valuesGateway
     * @param Chart $chart
     * @param SensorBuilder $builder
     * @param SensorVOBuilder $voBuilder
     */
    public function __construct(
        SensorGateway $gateway,
        SensorValuesGateway $valuesGateway,
        Chart $chart,
        SensorBuilder $builder,
        SensorVOBuilder $voBuilder
    ) {
        $this->gateway        = $gateway;
        $this->valuesGateway  = $valuesGateway;
        $this->chart          = $chart;
        $this->builder        = $builder;
        $this->voBuilder      = $voBuilder;
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

        $availableSensorIds = $this->gateway->getSensorIds();
        if (empty($activeSensorIds)) {
            $activeSensorIds = implode(':', $availableSensorIds);
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

        $sensorsRaw    = $this->gateway->getSensors();
        $sensorObjects = $this->builder->getSensors();

        foreach ($sensorsRaw as &$sensor) {
            $sensorId = $sensor['id'];

            if (!empty($sensor['last_value'])) {
                $sensorObj            = $sensorObjects[$sensor['type']];
                $sensor['espeak']     = (bool)$sensorObj->getEspeakText($sensor['last_value']);
                $sensor['last_value'] = $sensorObj->formatValue($sensor['last_value']);
            } else {
                $sensor['espeak'] = false;
            }

            if ($activeSensorIds && !in_array($sensorId, $activeSensorIds)) {
                continue;
            }
            $sensorValues[$sensorId] = $this->valuesGateway->getSensorValues($sensorId, $from);
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

        $sensorVo = $this->voBuilder->build(
            null,
            $name,
            $description,
            $interval,
            $node,
            $pin,
            $sensorType
        );

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
        unset($request);
        $sensor     = $this->gateway->getSensor($sensorId);
        $sensorObj  = $this->builder->build($sensor['type']);

        $text = $sensorObj->getEspeakText($sensor['last_value']);

        $espeakVo  = new EspeakVO($text);
        $event     = new EspeakEvent($espeakVo);
        $this->dispatchInBackground($event);

        return true;
    }

    /**
     * @Route("/sensors/slim/{sensor_id}/", name="sensor.slim")
     * @param Request $request
     * @param integer $sensorId
     * @return array
     */
    public function slim(Request $request, $sensorId)
    {
        unset($request);
        $sensor         = $this->gateway->getSensor($sensorId);
        $sensorObj      = $this->builder->build($sensor['type']);
        $formattedValue = $sensorObj->getEspeakText($sensor['last_value']);

        return [
            'sensor' => $sensor,
            'sensor_value_formatted' => $formattedValue,
            'sensor_obj' => $sensorObj,
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
        $sensorId        = $request->query->getInt('sensor_id');
        $sensor         = $this->gateway->getSensor($sensorId);
        $sensorObj      = $this->builder->build($sensor['type']);
        $formattedValue = $sensorObj->getEspeakText($sensor['last_value']);

        return [
            'sensor' => $sensor,
            'sensor_value_formatted' => $formattedValue,
            'sensor_obj' => $sensorObj,
            'refresh_interval' => 60
        ];
    }
}
