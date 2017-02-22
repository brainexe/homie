<?php

namespace Homie\Display\Annotation;

use BrainExe\Core\Annotations\Builder\ServiceDefinition;
use Symfony\Component\DependencyInjection\Definition;

class DisplayDeviceBuilder extends ServiceDefinition
{
    /**
     * {@inheritdoc}
     */
    public function setupDefinition(Definition $definition, string $serviceId)
    {
        $definition->setPublic(false);
        $definition->addTag(CompilerPass::TAG);
    }
}
