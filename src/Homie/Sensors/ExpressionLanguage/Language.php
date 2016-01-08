<?php

namespace Homie\Sensors\ExpressionLanguage;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use Homie\Sensors\SensorGateway;
use Homie\Sensors\SensorValueEvent;
use InvalidArgumentException;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @Service("Sensor.ExpressionLanguage.Language", tags={{"name"="expression_language"}}, public=false)
 */
class Language implements ExpressionFunctionProviderInterface
{
    /**
     * @var SensorGateway
     */
    private $gateway;

    /**
     * @Inject("@SensorGateway")
     * @param SensorGateway $gateway
     */
    public function __construct(SensorGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @return ExpressionFunction[] An array of Function instances
     */
    public function getFunctions()
    {
        yield new ExpressionFunction('getSensorValue', function ($sensorId) {
            return sprintf('$container->get("SensorGateway")->getSensor(%d)["lastValue"]', $sensorId);
        }, function (array $variables, $sensorId) {
            unset($variables);
            return $this->gateway->getSensor($sensorId)['lastValue'];
        });

        yield new ExpressionFunction('getSensor', function ($sensorId) {
            return sprintf('$container->get("SensorGateway")->getSensor(%d)', $sensorId);
        }, function (array $variables, $sensorId) {
            unset($variables);
            return $this->gateway->getSensor($sensorId);
        });

        yield new ExpressionFunction('isSensorValue', function ($sensorId) {
            return sprintf(
                "(\$eventName == '%s') && \$event->sensorVo->sensorId == %d",
                SensorValueEvent::VALUE,
                $sensorId
            );
        }, function ($parameters, $sensorId) {
            return $parameters['eventName'] === SensorValueEvent::VALUE &&
            $parameters['event']->sensorVo->sensorId == $sensorId;
        });
    }
}
