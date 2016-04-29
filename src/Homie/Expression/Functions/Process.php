<?php

namespace Homie\Expression\Functions;

use BrainExe\Annotations\Annotations\Inject;
use Generator;
use Homie\Client\ClientInterface;
use InvalidArgumentException;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;

/**
 * @ExpressionLanguageAnnotation("InputControl.Process")
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
        yield new ExpressionFunction('executeCommand', function (string $command, array $arguments = array()) {
            unset($command, $arguments);
            throw new InvalidArgumentException('Function executeCommand() not available as condition');
        }, function (array $variables, string $command, array $arguments = array()) {
            unset($variables);
            return $this->client->executeWithReturn($command, $arguments);
        });
    }
}
