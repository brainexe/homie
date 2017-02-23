<?php

namespace Homie\Sensors\Controller;

use BrainExe\Core\Annotations\Controller as ControllerAnnotation;

use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Traits\TimeTrait;
use Homie\Sensors\Builder;
use Homie\Sensors\GetValue\GetSensorValueEvent;
use Homie\Sensors\SensorGateway;
use Homie\Sensors\SensorValuesGateway;
use Iterator;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation(requirements={"sensorId":"\d+"})
 */
class Values
{

    use TimeTrait;
    use EventDispatcherTrait;

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
     * @param SensorGateway $gateway
     * @param SensorValuesGateway $valuesGateway
     * @param Builder $voBuilder
     */
    public function __construct(
        SensorGateway $gateway,
        SensorValuesGateway $valuesGateway,
        Builder $voBuilder
    ) {
        $this->gateway        = $gateway;
        $this->valuesGateway  = $valuesGateway;
        $this->voBuilder      = $voBuilder;
    }

    /**
     * @param Request $request
     * @return Iterator
     * @Route("/sensors/byTime/", name="sensor.getByTime")
     */
    public function getByTime(Request $request) : Iterator
    {
        $sensorIds = (string)$request->query->get('sensorIds');
        $time      = $request->query->getInt('timestamp');

        if (empty($time)) {
            $time = $this->getTime()->now();
        }

        return $this->valuesGateway->getByTime(explode(',', $sensorIds), $time);
    }

    /**
     * @param Request $request
     * @param int $sensorId
     * @return bool
     * @Route("/sensors/{sensorId}/force/", name="sensor.forceGetValue", methods="POST")
     */
    public function forceGetValue(Request $request, int $sensorId) : bool
    {
        unset($request);
        $sensor   = $this->gateway->getSensor($sensorId);
        $sensorVo = $this->voBuilder->buildFromArray($sensor);

        $event = new GetSensorValueEvent($sensorVo);
        $this->dispatchInBackground($event);

        return true;
    }

    /**
     * @param Request $request
     * @param int $sensorId
     * @Route("/sensors/{sensorId}/value/", name="sensor.value", methods="GET", options={"cache":10})
     * @return array
     */
    public function getValue(Request $request, int $sensorId) : array
    {
        return $this->gateway->getSensor($sensorId);
    }

    /**
     * @param Request $request
     * @param int $sensorId
     * @param int $timestamp
     * @return bool
     * @Route("/sensors/{sensorId}/values/{timestamp}/", name="sensor.value.delete", methods="DELETE")
     */
    public function delete(Request $request, int $sensorId, int $timestamp) : bool
    {
        return $this->valuesGateway->deleteValue($sensorId, $timestamp);
    }

    /**
     * @param Request $request
     * @param int $sensorId
     * @return bool
     * @Route("/sensors/{sensorId}/value/", name="sensor.submitValue", methods="POST")
     */
    public function addValue(Request $request, int $sensorId) : bool
    {
        $value    = $request->request->get('value');
        $sensor   = $this->gateway->getSensor($sensorId);
        $sensorVo = $this->voBuilder->buildFromArray($sensor);

        $this->valuesGateway->addValue($sensorVo, $value);

        return true;
    }
}
