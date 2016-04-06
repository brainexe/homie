<?php

namespace Homie\EggTimer;

use BrainExe\Annotations\Annotations\Inject;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Generator;
use InvalidArgumentException;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @ExpressionLanguageAnnotation("EggTimer.ExpressionLanguage")
 */
class ExpressionLanguage implements ExpressionFunctionProviderInterface
{

    use EventDispatcherTrait;

    /**
     * @var EggTimer
     */
    private $eggTimer;

    /**
     * @Inject("@EggTimer")
     * @param EggTimer $timer
     */
    public function __construct(EggTimer $timer)
    {
        $this->eggTimer = $timer;
    }

    /**
     * @return Generator|ExpressionFunction[]
     */
    public function getFunctions()
    {
        yield new ExpressionFunction('eggTimer', function () {
            throw new InvalidArgumentException('eggTimer() is not available in this context');
        }, function (array $variables, $time, $text) {
            unset($variables);
            $this->eggTimer->addNewJob($time, $text);
        });
    }
}
