<?php

namespace Homie\Sensors\Annotation;

use BrainExe\Annotations\Annotations\Service;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @Annotation
 */
class Sensor extends Service
{
    /**
     * {@inheritdoc}
     */
    public static function getBuilder(ContainerBuilder $container, Reader $reader)
    {
        return new SensorBuilder($container, $reader);
    }
}
