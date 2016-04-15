<?php

namespace Homie\Gpio;

use BrainExe\Annotations\Annotations\Inject;
use Generator;
use InvalidArgumentException;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;

/**
 * @ExpressionLanguageAnnotation("Gpio.ExpressionLanguage")
 */
class ExpressionLanguage implements ExpressionFunctionProviderInterface
{

    /**
     * @var GpioManager
     */
    private $manager;

    /**
     * @Inject({"@GPIO.GpioManager"})
     * @param GpioManager $manager
     */
    public function __construct(GpioManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return Generator|ExpressionFunction[]
     */
    public function getFunctions()
    {
        yield new ExpressionFunction('setGPIOPin', function (int $pin, $status, $value) {
            unset($pin, $status, $value);
            throw new InvalidArgumentException('setGPIOPin() is not available in this context');
        }, function (array $variables, string $pin, $status, $value) {
            unset($variables);
            $this->manager->setPin($pin, $status, $value);
        });
    }
}
