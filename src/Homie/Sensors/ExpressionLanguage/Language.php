<?php

namespace Homie\Sensors\ExpressionLanguage;

use BrainExe\Annotations\Annotations\Inject;
use Homie\Sensors\SensorGateway;
use Homie\Sensors\SensorValueEvent;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;

/**
 * @ExpressionLanguageAnnotation("Sensor.ExpressionLanguage.Language")
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
        return [
            $this->getSensorValue(),
            $this->getSensor(),
            $this->isSensorValue()
        ];
    }

    private function getSensorValue()
    {
        return new ExpressionFunction('getSensorValue', function (int $sensorId) {
            return sprintf('$container->get("SensorGateway")->getSensor(%d)["lastValue"]', $sensorId);
        }, function (array $variables, int $sensorId) {
            unset($variables);
            return $this->gateway->getSensor($sensorId)['lastValue'];
        });
    }

    private function getSensor()
    {
        return new ExpressionFunction('getSensor', function (int $sensorId) {
            return sprintf('$container->get("SensorGateway")->getSensor(%d)', $sensorId);
        }, function (array $variables, int $sensorId) {
            unset($variables);
            return $this->gateway->getSensor($sensorId);
        });
    }

    private function isSensorValue()
    {
        return new ExpressionFunction('isSensorValue', function (int $sensorId) {
            return sprintf(
                "(\$eventName == '%s') && \$event->sensorVo->sensorId == %d",
                SensorValueEvent::VALUE,
                $sensorId
            );
        }, function (array $parameters, int $sensorId) {
            return $parameters['eventName'] === SensorValueEvent::VALUE &&
            $parameters['event']->sensorVo->sensorId == $sensorId;
        });
    }
}
