<?php

namespace Homie\Display\Annotation;

use BrainExe\Annotations\Annotations\Service;
use Doctrine\Common\Annotations\Reader;

/**
 * @Annotation
 */
class DisplayDevice extends Service
{
    /**
     * {@inheritdoc}
     */
    public static function getBuilder(Reader $reader)
    {
        return new DisplayDeviceBuilder($reader);
    }
}
