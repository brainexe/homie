<?php

namespace Homie\Display\Annotation;

use BrainExe\Core\Annotations\Service;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @Annotation
 */
class DisplayDevice extends Service
{
    /**
     * {@inheritdoc}
     */
    public static function getBuilder(ContainerBuilder $container, Reader $reader)
    {
        return new DisplayDeviceBuilder($container, $reader);
    }
}
