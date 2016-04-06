<?php

namespace Homie\Expression\Annotation;

use BrainExe\Annotations\Builder\ServiceDefinition;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Definition;

class ExpressionLanguageBuilder extends ServiceDefinition
{
    /**
     * {@inheritdoc}
     */
    public function build(ReflectionClass $reflectionClass, $annotation)
    {
        /** @var Definition $definition */
        list($serviceId, $definition) = parent::build($reflectionClass, $annotation);

        $definition->setPublic(false);
        $definition->addTag('expression_language'); // todo

        return [$serviceId, $definition];
    }
}
