<?php

namespace Homie\Sensors\ExpressionLanguage;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use Homie\Sensors\SensorGateway;
use Prophecy\Exception\InvalidArgumentException;
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
        $sensorValue = new ExpressionFunction('getSensorValue', function () {
            throw new InvalidArgumentException('getSensorValue not implemented');
        }, function (array $variables, $value) {
            return $this->gateway->getSensor($value)['lastValue'];
        });

        $sensor = new ExpressionFunction('getSensor', function () {
            throw new InvalidArgumentException('getSensor not implemented');
        }, function (array $variables, $value) {
            return $this->gateway->getSensor($value);
        });

        return [$sensorValue, $sensor];
    }
}
