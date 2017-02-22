<?php

namespace Homie\Sensors\CompilerPass\Annotation;

use BrainExe\Core\Annotations\Service;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @Annotation
 */
class SensorFormatter extends Service
{
    /**
     * {@inheritdoc}
     */
    public static function getBuilder(ContainerBuilder $container, Reader $reader)
    {
        return new FormatterBuilder($container, $reader);
    }
}
