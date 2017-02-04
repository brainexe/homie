<?php

namespace Homie\Sensors\Controller;


use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Authentication\Settings\Settings;
use BrainExe\Core\Traits\FileCacheTrait;
use BrainExe\Core\Traits\TimeTrait;
use Homie\Sensors\Builder;
use Homie\Sensors\Chart;
use Homie\Sensors\SensorGateway;
use Homie\Sensors\SensorValuesGateway;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("Sensors.Controller.Controller", requirements={"sensorId":"\d+"})
 */
class Controller
{

    use TimeTrait;
    use FileCacheTrait;

    const SETTINGS_ACTIVE_SENSORS = 'sensors:active_sensors';
    const SETTINGS_TIMESPAN       = 'sensors:timespan';

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
     * @var Chart
     */
    private $chart;

    /**
     * @var Settings
     */
    private $settings;

    /**
     * @param SensorGateway $gateway
     * @param SensorValuesGateway $valuesGateway
     * @param Chart $chart
     * @param Builder $voBuilder
     * @param Settings $settings
     */
    public function __construct(
        SensorGateway $gateway,
        SensorValuesGateway $valuesGateway,
        Chart $chart,
        Builder $voBuilder,
        Settings $settings
    ) {
        $this->gateway        = $gateway;
        $this->valuesGateway  = $valuesGateway;
        $this->chart          = $chart;
        $this->voBuilder      = $voBuilder;
        $this->settings       = $settings;
    }

    /**
     * @return array
     * @Route("/sensors/", name="sensor.index", methods="GET")
     */
    public function sensors()
    {
        return [
            'types'         => $this->includeFile('sensors'),
            'formatters'    => $this->includeFile('sensor_formatter'),
            'fromIntervals' => Chart::getTimeSpans(),
            'sensors'       => array_map(
                [$this->voBuilder, 'buildFromArray'],
                $this->gateway->getSensors()
            )
        ];
    }

    /**
     * @param Request $request
     * @param string $activeSensorIds
     * @return array
     * @Route(
     *     "/sensors/load/{activeSensorIds}/",
     *     name="sensor.loadall",
     *     requirements={"activeSensorIds":"[\d:]+"}
     * )
     */
    public function indexSensor(Request $request, string $activeSensorIds) : array
    {
        $userId = $request->attributes->getInt('user_id');
        $ago    = $this->getAgo($request, $userId);
        $now    = $this->getTime()->now();

        $activeSensorIds = $this->getActiveSensorIds($activeSensorIds, $userId);
        if ($request->query->get('save')) {
            $this->settings->set($userId, self::SETTINGS_ACTIVE_SENSORS, $activeSensorIds);
            $this->settings->set($userId, self::SETTINGS_TIMESPAN, $ago);
        }

        $sensorsRaw   = $this->gateway->getSensors($activeSensorIds);
        $sensorValues = $this->getValues($sensorsRaw, $now - $ago, $now);

        $json = $this->chart->formatJsonData($sensorsRaw, $sensorValues);

        return [
            'json' => iterator_to_array($json),
            'ago'  => $ago,
            'from' => $now - $ago,
            'to'   => $now
        ];
    }

    /**
     * @param array $sensorsRaw
     * @param int $from
     * @param int $to
     * @return array
     */
    private function getValues(array $sensorsRaw, int $from, int $to) : array
    {
        $sensorValues = [];
        foreach ($sensorsRaw as $sensor) {
            $sensorId = $sensor['sensorId'];
            $sensorValues[$sensorId] = $this->valuesGateway->getSensorValues($sensorId, $from, $to);
        }

        return $sensorValues;
    }

    /**
     * @param string $activeSensorIds
     * @param int $userId
     * @return int[]
     */
    private function getActiveSensorIds(string $activeSensorIds, int $userId) : array
    {
        $activeSensorIds = array_filter(array_unique(array_map('intval', explode(':', $activeSensorIds))));

        if (empty($activeSensorIds)) {
            $activeSensorIds = (array)$this->settings->get($userId, self::SETTINGS_ACTIVE_SENSORS);
        }

        if (empty($activeSensorIds)) {
            // show all sensors as default
            $activeSensorIds = $this->gateway->getSensorIds();
        }

        return $activeSensorIds;
    }

    /**
     * @param Request $request
     * @param int $userId
     * @return int
     */
    private function getAgo(Request $request, int $userId) : int
    {
        $from = $request->query->getInt('from');
        if (!$from) {
            return (int)$this->settings->get($userId, self::SETTINGS_TIMESPAN) ?: Chart::DEFAULT_TIME;
        }

        return $from;
    }
}
