<?php

namespace Homie\EggTimer;

use BrainExe\Annotations\Annotations\Inject;
use Homie\Expression\Action;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Generator;
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
        yield new Action('eggTimer', function (array $variables, string $time, string $text = '') {
            unset($variables);
            $this->eggTimer->addNewJob($time, $text);
        });
    }
}
