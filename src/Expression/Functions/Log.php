<?php

namespace Homie\Expression\Functions;

use BrainExe\Core\Traits\LoggerTrait;
use Generator;
use Homie\Expression\Action;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @ExpressionLanguageAnnotation
 */
class Log implements ExpressionFunctionProviderInterface
{

    use LoggerTrait;

    /**
     * @return Generator|ExpressionFunction[] An array of Function instances
     */
    public function getFunctions()
    {
        yield new Action('log', function (array $parameters, $level, string $message, string $context = null) {
            unset($parameters);
            $this->log($level, $message, ['channel' => $context]);
        });
    }
}
