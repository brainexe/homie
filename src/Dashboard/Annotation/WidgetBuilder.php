<?php

namespace Homie\Dashbaord\Annotation;

use BrainExe\Core\Annotations\Builder\ServiceDefinition;
use Homie\Dashboard\WidgetCompilerPass as CompilerPass;
use Symfony\Component\DependencyInjection\Definition;

class WidgetBuilder extends ServiceDefinition
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
