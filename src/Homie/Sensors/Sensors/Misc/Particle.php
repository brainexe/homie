<?php

namespace Homie\Sensors\Sensors\Misc;

use BrainExe\Annotations\Annotations\Inject;
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
 * {"deviceId":"d957040100c6c8ec4272cb6b","accessToken":"a553f7f40df8e4b15b68fb2cb01e6940b472192f"}
 *
 * @Sensor("Sensor.Misc.Particle")
 * @todo searchable
 */
class Particle extends AbstractSensor
{
    const TYPE = 'custom.particle';

    /**
     * @var Client
     */
    private $client;

    /**
     * @Inject({"@WebserviceClient"})
     * @param Client $client
     */
    public function __construct(
        Client $client
    ) {
        $this->client  = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(SensorVO $sensor) : float
    {
        $nodeId = $sensor->node;

        // TODO
        $node = new Node();
        $options = $node->getOptions();

        $url = sprintf(self::FUNCTION_URL, $options['deviceId'], $sensor->parameter, 'accessToken');

        /** @var ResponseInterface $response */
        $response = $this->client->request('POST', $url, ['timeout' => 10]);

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

        return $definition;
    }
}
