<?php

namespace Homie\Sensors\Sensors;

use BrainExe\Annotations\Annotations\Inject;
use Homie\Client\ClientInterface;
use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Formatter\None;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Sensor("Sensor.Brightness")
 */
class Brightness extends AbstractSensor
{

    const TYPE = 'brightness';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @Inject({"@HomieClient"})
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param integer $path
     * @return double
     */
    public function getValue($path)
    {
        $command = sprintf(
            "fswebcam /tmp/brightness.jpg;".
            "convert /tmp/brightness.jpg  -colorspace gray  -resize 1x1  txt:-"
        );

        $result = $this->client->executeWithReturn($command);

        if (!preg_match('/gray\((\d+)\)/', $result, $matches)) {
            return 0;
        }

        return $this->round($matches[1], 0.1);
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported($parameter, OutputInterface $output)
    {
        // todo check if camera is connected
        return true;
    }

    /**
     * @return Definition
     */
    public function getDefinition()
    {
        $definition            = new Definition();
        $definition->name      = gettext('Brightness');
        $definition->type      = Definition::TYPE_NONE;
        $definition->formatter = None::TYPE;
        $definition->neededPackages = [
            'fswebcam',
            'imagemagick'
        ];

        return $definition;
    }
}
