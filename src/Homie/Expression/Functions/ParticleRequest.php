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
    const FUNCTION_URL = 'https://api.particle.io/v1/devices/%s/%s?access_token=%s&args=%s';

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
        $this->node = $node;
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

            $node    = $this->node->get($nodeId);
            $options = $node->getOptions();

            $url = sprintf(
                self::FUNCTION_URL,
                $options['deviceId'],
                $function,
                $options['accessToken'],
                $args
            );

            try {
                /** @var Response $response */
                $response = $this->client->request('POST', $url, $options);

                $json = json_decode($response->getBody()->getContents(), true);

                return $json['return_value'];
            } catch (Exception $e) {
                return $e->getMessage();
            }
        });
    }
}
