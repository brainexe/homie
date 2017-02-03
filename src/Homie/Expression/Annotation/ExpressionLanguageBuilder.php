<?php

namespace Homie\Expression\Annotation;

use BrainExe\Annotations\Builder\ServiceDefinition;
use Homie\Expression\CompilerPass\RegisterProvider;
use Symfony\Component\DependencyInjection\Definition;

class ExpressionLanguageBuilder extends ServiceDefinition
{

    /**
     * {@inheritdoc}
     */
    public function setupDefinition(Definition $definition, string $serviceId)
    {
        $definition->setPublic(false);
        $definition->addTag(RegisterProvider::TAG);
    }
}
