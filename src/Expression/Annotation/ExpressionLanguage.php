<?php

namespace Homie\Expression\Annotation;

use BrainExe\Core\Annotations\Service;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @Annotation
 */
class ExpressionLanguage extends Service
{
    /**
     * {@inheritdoc}
     */
    public static function getBuilder(ContainerBuilder $container, Reader $reader)
    {
        return new ExpressionLanguageBuilder($container, $reader);
    }
}
