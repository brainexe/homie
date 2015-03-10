<?php

namespace Raspberry\Sensors;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Raspberry\Espeak\EspeakEvent;
use Raspberry\Espeak\EspeakVO;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("SensorsController")
 */
class Controller
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
     * @var Builder
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
     * @Inject({"@SensorGateway", "@SensorValuesGateway", "@Chart", "@SensorBuilder", "@Sensor.VOBuilder"})
     * @param SensorGateway $gateway
     * @param SensorValuesGateway $valuesGateway
     * @param Chart $chart
     * @param SensorBuilder $builder
     * @param Builder $voBuilder
     */
    public function __construct(
        SensorGateway $gateway,
        SensorValuesGateway $valuesGateway,
        Chart $chart,
        SensorBuilder $builder,
        Builder $voBuilder
    ) {
        $this->gateway        = $gateway;
        $this->valuesGateway  = $valuesGateway;
        $this->chart          = $chart;
        $this->builder        = $builder;
        $this->voBuilder      = $voBuilder;
    }

    /**
     * @return array
     * @Route("/sensors/")
     */
    public function sensors()
    {
        return [
            'types'   => $this->builder->getSensors(),
            'sensors' => $this->gateway->getSensors()
        ];
    }

    /**
     * @todo cleanup
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

        $activeSensorIds = array_unique(array_map('intval', explode(':', $activeSensorIds)));

        $sensorValues = [];

        $sensorsRaw    = $this->gateway->getSensors();
        $sensorObjects = $this->builder->getSensors();

        foreach ($sensorsRaw as &$sensor) {
            $sensorId = $sensor['sensorId'];

            if (!empty($sensor['lastValue'])) {
                $formatter = $this->builder->getFormatter($sensor['type']);
                $sensor['espeak']    = (bool)$formatter->getEspeakText($sensor['lastValue']);
                $sensor['lastValue'] = $formatter->formatValue($sensor['lastValue']);
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
            'active_sensor_ids' => array_values($activeSensorIds),
            'json' => $json,
            'current_from' => $from,
            'available_sensors' => $sensorObjects,
            'fromIntervals' => [
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
        $formatter  = $this->builder->getFormatter($sensor['type']);
        $text       = $formatter->getEspeakText($sensor['lastValue']);

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
        $formatter      = $this->builder->getFormatter($sensor['type']);
        $formattedValue = $formatter->getEspeakText($sensor['lastValue']);

        return [
            'sensor'                 => $sensor,
            'sensor_value_formatted' => $formattedValue,
            'refresh_interval'       => 60
        ];
    }

    /**
     * @Route("/sensors/delete/", name="sensor.delete")
     * @param Request $request
     * @return bool
     */
    public function delete(Request $request)
    {
        $sensorId = $request->request->getInt('sensorId');

        $this->gateway->deleteSensor($sensorId);

        return true;
    }
}
