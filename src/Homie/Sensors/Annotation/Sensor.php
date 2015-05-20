<?php

namespace Homie\Sensors\Annotation;

use BrainExe\Annotations\Annotations\Service;
use Doctrine\Common\Annotations\Reader;

/**
 * @Annotation
 */
class Sensor extends Service
{
    /**
     * {@inheritdoc}
     */
    public static function getBuilder(Reader $reader)
    {
        return new SensorBuilder($reader);
    }
}
