<?php

namespace Homie\Sensors\Controller;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Application\UserException;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Homie\Sensors\Builder;
use Homie\Sensors\Exception\SensorException;
use Homie\Sensors\GetValue\GetSensorValueEvent;
use Homie\Sensors\Interfaces\Parameterized;
use Homie\Sensors\Interfaces\Searchable;
use Homie\Sensors\SensorBuilder;
use Homie\Sensors\SensorGateway;
use Homie\Sensors\SensorVO;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("Sensors.Controller.Administration", requirements={"sensorId":"\d+"})
 */
class Administration
{
    use EventDispatcherTrait;

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
        $this->gateway   = $gateway;
        $this->builder   = $builder;
        $this->voBuilder = $voBuilder;
    }

    /**
     * @param Request $request
     * @return SensorVO
     * @Route("/sensors/", name="sensors.add", methods="POST")
     */
    public function addSensor(Request $request) : SensorVO
    {
        $sensorType  = (string)$request->request->getAlpha('type');
        $name        = $request->request->get('name');
        $description = $request->request->get('description');
        $parameter   = $request->request->get('parameter');
        $interval    = $request->request->getInt('interval');
        $node        = $request->request->getInt('node');
        $color       = $request->request->get('color') ?? '#aaaaaa';  // todo random color
        $formatter   = (string)$request->request->getAlnum('formatter');
        $tags        = (array)$request->request->get('tags');

        $sensorVo = $this->voBuilder->build(
            null,
            $name,
            $description,
            $interval,
            $node,
            $parameter,
            $sensorType,
            $color,
            $formatter,
            $tags
        );

        $this->gateway->addSensor($sensorVo);

        $event = new GetSensorValueEvent($sensorVo);
        $this->dispatchInBackground($event);

        return $sensorVo;
    }

    /**
     * @Route("/sensors/{sensorId}/", name="sensor.delete", methods="DELETE")
     * @param int $sensorId
     * @param Request $request
     * @return bool
     */
    public function delete(Request $request, int $sensorId)
    {
        unset($request);

        $this->gateway->deleteSensor($sensorId);

        return true;
    }

    /**
     * @Route("/sensors/{sensorId}/", name="sensor.edit", methods="PUT")
     * @param Request $request
     * @param int $sensorId
     * @return SensorVO
     */
    public function edit(Request $request, int $sensorId) : SensorVO
    {
        $sensor                = $this->gateway->getSensor($sensorId);
        $sensorVo              = $this->voBuilder->buildFromArray($sensor);

        $sensorVo->type        = $request->request->get('type');
        $sensorVo->name        = $request->request->get('name');
        $sensorVo->description = $request->request->get('description');
        $sensorVo->parameter   = $request->request->get('parameter');
        $sensorVo->interval    = $request->request->getInt('interval');
        $sensorVo->color       = $request->request->get('color');
        $sensorVo->formatter   = $request->request->get('formatter');
        $sensorVo->tags        = (array)$request->request->get('tags');

        $this->gateway->save($sensorVo);

        return $sensorVo;
    }

    /**
     * @param Request $request
     * @param string $sensorType
     * @return string[]|bool
     * @throws UserException
     * @Route("/sensors/{sensorType}/parameters/", name="sensor.search", methods="GET")
     */
    public function parameters(Request $request, string $sensorType)
    {
        unset($request);

        $sensor = $this->builder->build($sensorType);
        if (!$sensor instanceof Parameterized) {
            return false;
        }

        if (!$sensor instanceof Searchable) {
            return true;
        }

        return $sensor->search();
    }

    /**
     * @param Request $request
     * @param string $sensorType
     * @param string $parameter
     * @return string[]
     * @Route("/sensors/{sensorType}/{parameter}/valid/", name="sensor.valid", methods="GET")
     */
    public function isValid(Request $request, string $sensorType, string $parameter) : array
    {
        unset($request);

        $sensor = $this->builder->build($sensorType);

        $sensorVo = new SensorVO();
        $sensorVo->parameter = $parameter;

        try {
            return [
                'isValid' => $sensor->isSupported($sensorVo),
                'message' => ''
            ];
        } catch (SensorException $e) {
            return [
                'isValid' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
