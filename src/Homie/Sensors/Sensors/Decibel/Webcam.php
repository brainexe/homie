<?php

namespace Homie\Sensors\Sensors\Decibel;

use BrainExe\Annotations\Annotations\Inject;
use Homie\Client\ClientInterface;
use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Formatter\None;
use Homie\Sensors\Sensors\AbstractSensor;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Sensor("Sensor.Decibel.Webcam")
 */
class Webcam extends AbstractSensor
{

    const TYPE = 'decibel.webcam';

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
        $tmp = sys_get_temp_dir();

        $this->client->execute(sprintf('arecord -d 1 %s/tmp_rec.wav', $tmp));

        $command = sprintf(
            "sox -t .wav %s/tmp_rec.wav -n stat 2>&1 " .
            "| grep \"Maximum amplitude\" | cut -d ':' -f 2",
            $tmp
        );

        $value = (float)trim($this->client->executeWithReturn($command));

        $value = 20 * log($value) / log(10);

        return round($value, 1);
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported($parameter, OutputInterface $output)
    {
        // todo check if micro is connected
        return true;
    }

    /**
     * @return Definition
     */
    public function getDefinition()
    {
        $definition            = new Definition();
        $definition->name      = gettext('DB');
        $definition->type      = Definition::TYPE_NONE;
        $definition->formatter = None::TYPE;
        $definition->neededPackages = [
            'arecord',
            'sox'
        ];

        return $definition;
    }
}
