<?php

namespace Homie\Sensors\ExpressionLanguage;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use Homie\Sensors\SensorGateway;
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
        $sensorValue = new ExpressionFunction('getSensorValue', function ($sensorId) {
            return sprintf('$container->get("SensorGateway")->getSensor(%d)["lastValue"]', $sensorId);
        }, function (array $variables, $sensorId) {
            return $this->gateway->getSensor($sensorId)['lastValue'];
        });

        $sensor = new ExpressionFunction('getSensor', function ($sensorId) {
            return sprintf('$container->get("SensorGateway")->getSensor(%d)', $sensorId);
        }, function (array $variables, $sensorId) {
            return $this->gateway->getSensor($sensorId);
        });

        return [$sensorValue, $sensor];
    }
}
