<?php

namespace Homie\Expression\Functions;

use BrainExe\Core\Annotations\Inject;
use Generator;
use Homie\Client\ClientInterface;
use Homie\Expression\Action;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @ExpressionLanguageAnnotation("Expression.Functions.Process")
 * @deprecated too insecure!
 */
class Process implements ExpressionFunctionProviderInterface
{

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @Inject("@HomieClient")
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @return Generator|ExpressionFunction[] An array of Function instances
     */
    public function getFunctions()
    {
        yield new Action('executeCommand', function (array $variables, string $command, array $arguments = array()) {
            unset($variables);
            return $this->client->executeWithReturn($command, $arguments);
        });
    }
}
