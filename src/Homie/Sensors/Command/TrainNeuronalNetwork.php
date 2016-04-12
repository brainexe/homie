<?php

namespace Homie\Sensors\Command;

use Symfony\Component\Console\Command\Command;
use BrainExe\Core\Annotations\Command as CommandAnnotation;

/**
 * @CommandAnnotation("Command.Sensors.TrainNeuronalNetwork")
 */
class TrainNeuronalNetwork extends Command
{
    
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sensor:train');
    }

}
