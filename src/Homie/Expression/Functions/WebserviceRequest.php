<?php

namespace Homie\Expression\Functions;

use BrainExe\Annotations\Annotations\Inject;
use Generator;
use GuzzleHttp\Client;
use Homie\Expression\Action;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @ExpressionLanguageAnnotation("Expression.Functions.WebserviceRequest")
 */
class WebserviceRequest implements ExpressionFunctionProviderInterface
{

    /**
     * @var Client
     */
    private $client;

    /**
     * @Inject("@WebserviceClient")
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return Generator|ExpressionFunction[] An array of Function instances
     */
    public function getFunctions()
    {
        yield new Action('webserviceRequest', function (
            array $variables,
            string $url,
            string $method = 'GET',
            array $options = []
        ) {
            unset($variables);

            return $this->client->request($method, $url, $options);
        });
    }
}
