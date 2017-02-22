<?php

namespace Homie\Sensors\Sensors\Misc;

use BrainExe\Core\Annotations\Inject;
use GuzzleHttp\Client;
use Homie\Node;
use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Exception\InvalidSensorValueException;
use Homie\Sensors\Formatter\None;
use Homie\Sensors\Interfaces\Searchable;
use Homie\Sensors\Sensors\AbstractSensor;
use Homie\Sensors\SensorVO;
use Psr\Http\Message\ResponseInterface;

/**
 * @Sensor("Sensor.Misc.Metawear")
 */
class Metawear extends AbstractSensor implements Searchable
{

    const TYPE = 'custom.metawear';

    const SUPPORTED_TYPES = [
        'temperature',
        'pressure',
        'brightness'
    ];

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var Client
     */
    private $client;

    /**
     * @Inject({"@WebserviceClient", "%metawear.url%"})
     * @param Client $client
     * @param string $url
     */
    public function __construct(
        Client $client,
        string $url
    ) {
        $this->client  = $client;
        $this->baseUrl = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(SensorVO $sensor) : float
    {
        $url = sprintf('%s/%s/', $this->baseUrl, $sensor->parameter);

        /** @var ResponseInterface $response */
        $response = $this->client->request('GET', $url, ['timeout' => 5]);

        $body = $response->getBody();
        if ($response->getStatusCode() != 200) {
            throw new InvalidSensorValueException($sensor, sprintf('Invalid metawear response: %s', $body));
        }

        return (float)$body;
    }

    /**
     * @return Definition
     */
    public function getDefinition() : Definition
    {
        $definition            = new Definition();
        $definition->type      = Definition::TYPE_NONE;
        $definition->formatter = None::TYPE;
        $definition->requiredNode = [Node::TYPE_METAWEAR];

        return $definition;
    }

    /**
     * @return string[]
     */
    public function search() : array
    {
        return self::SUPPORTED_TYPES;
    }
}
