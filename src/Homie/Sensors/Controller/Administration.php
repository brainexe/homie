<?php

namespace Homie\Sensors\Controller;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use Homie\Sensors\Builder;
use Homie\Sensors\SensorBuilder;
use Homie\Sensors\SensorGateway;
use Homie\Sensors\SensorVO;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("Sensors.Controller.Administration")
 */
class Administration
{

    /**
     * @var SensorGateway
     */
    private $gateway;

    /**
     * @var Builder
     */
    private $voBuilder;

    /**
     * @var SensorBuilder;
     */
    private $builder;

    /**
     * @Inject({
     *  "@SensorGateway",
     *  "@SensorBuilder",
     *  "@Sensor.VOBuilder",
     * })
     * @param SensorGateway $gateway
     * @param SensorBuilder $builder
     * @param Builder $voBuilder
     */
    public function __construct(
        SensorGateway $gateway,
        SensorBuilder $builder,
        Builder $voBuilder
    ) {
        $this->gateway        = $gateway;
        $this->builder        = $builder;
        $this->voBuilder      = $voBuilder;
    }

    /**
     * @param Request $request
     * @return SensorVO
     * @Route("/sensors/", name="sensors.add", methods="POST")
     */
    public function addSensor(Request $request)
    {
        $sensorType  = $request->request->get('type');
        $name        = $request->request->get('name');
        $description = $request->request->get('description');
        $pin         = $request->request->get('pin');
        $interval    = $request->request->getInt('interval');
        $node        = $request->request->getInt('node');
        $color       = $request->request->get('color');
        $formatter   = $request->request->get('formatter');

        $sensorVo = $this->voBuilder->build(
            null,
            $name,
            $description,
            $interval,
            $node,
            $pin,
            $sensorType,
            $color,
            $formatter
        );

        $this->gateway->addSensor($sensorVo);

        return $sensorVo;
    }

    /**
     * @Route("/sensors/{sensorId}/", name="sensor.delete", methods="DELETE")
     * @param int $sensorId
     * @param Request $request
     * @return bool
     */
    public function delete(Request $request, $sensorId)
    {
        unset($request);

        $this->gateway->deleteSensor($sensorId);

        return true;
    }

    /**
     * @Route("/sensors/{sensorId}/", name="sensor.edit", methods="PUT")
     * @param int $sensorId
     * @param Request $request
     * @return SensorVO
     */
    public function edit(Request $request, $sensorId)
    {
        $sensor   = $this->gateway->getSensor($sensorId);
        $sensorVo = $this->voBuilder->buildFromArray($sensor);
        $sensorVo->type        = $request->request->get('type');
        $sensorVo->name        = $request->request->get('name');
        $sensorVo->description = $request->request->get('description');
        $sensorVo->pin         = $request->request->get('pin');
        $sensorVo->interval    = $request->request->getInt('interval');
        $sensorVo->color       = $request->request->get('color');
        $sensorVo->formatter   = $request->request->get('formatter');

        $this->gateway->save($sensorVo);

        return $sensorVo;
    }
}
