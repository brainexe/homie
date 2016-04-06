<?php

namespace Homie\Expression\Annotation;

use BrainExe\Annotations\Annotations\Service;
use Doctrine\Common\Annotations\Reader;

/**
 * @Annotation
 */
class ExpressionLanguage extends Service
{
    /**
     * {@inheritdoc}
     */
    public static function getBuilder(Reader $reader)
    {
        return new ExpressionLanguageBuilder($reader);
    }
}
