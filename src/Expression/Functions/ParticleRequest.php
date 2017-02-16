<?php

namespace Homie\Expression\Functions;

use BrainExe\Annotations\Annotations\Inject;
use Exception;
use Generator;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Homie\Expression\Action;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;
use Homie\Node\Gateway;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @ExpressionLanguageAnnotation("Expression.Functions.ParticleRequest")
 */
class ParticleRequest implements ExpressionFunctionProviderInterface
{
    const DEVICE_URL   = 'https://api.particle.io/v1/devices/%s?access_token=%s&args=%s';
    const FUNCTION_URL = 'https://api.particle.io/v1/devices/%s/%s?access_token=%s&format=raw&args=%s';

    /**
     * @var Client
     */
    private $client;

    /**
     * @var Gateway
     */
    private $node;

    /**
     * @Inject({
     *     "@WebserviceClient",
     *     "@Node.Gateway"
     * })
     * @param Client $client
     * @param Gateway $node
     */
    public function __construct(Client $client, Gateway $node)
    {
        $this->client = $client;
        $this->node   = $node;
    }

    /**
     * @return Generator|ExpressionFunction[] An array of Function instances
     */
    public function getFunctions()
    {
        yield new Action('callParticleFunction', function (
            array $variables,
            int $nodeId,
            string $function,
            string $args = ''
        ) {
            unset($variables);

            $node = $this->node->get($nodeId);

            $url = sprintf(
                self::FUNCTION_URL,
                $node->getOption('deviceId'),
                $function,
                $node->getOption('accessToken'),
                $args
            );

            return $this->makeRequest('POST', $url);
        });

        yield new Action('getParticleFunction', function (
            array $variables,
            int $nodeId
        ) {
            unset($variables);

            $node = $this->node->get($nodeId);

            $url = sprintf(
                self::DEVICE_URL,
                $node->getOption('deviceId'),
                $node->getOption('accessToken')
            );

            $data = json_decode($this->makeRequest('GET', $url), true);

            return $data['functions'];
        });
    }

    /**
     * @param string $method
     * @param string $url
     * @return string
     */
    private function makeRequest(string $method, string $url)
    {
        try {
            /** @var Response $response */
            $response = $this->client->request($method, $url);

            return $response->getBody()->getContents();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

}
