<?php

namespace Tests\Homie\Expression;

use Homie\Expression\CompilerPass;
use Homie\Expression\CompilerPass\RegisterProvider;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class CompilerPassTest extends TestCase
{

    public function testProcess()
    {
        $subject = new RegisterProvider();

        /** @var ContainerBuilder|MockObject $container */
        $container  = $this->getMock(ContainerBuilder::class, ['getDefinition', 'findTaggedServiceIds']);
        $definition = $this->getMock(Definition::class);
        $language   = $this->getMock(Definition::class);

        $container
            ->expects($this->at(0))
            ->method('getDefinition')
            ->with('EventDispatcher')
            ->willReturn($definition);
        $container
            ->expects($this->at(1))
            ->method('getDefinition')
            ->with('Expression.Language')
            ->willReturn($language);
        $container
            ->expects($this->at(2))
            ->method('findTaggedServiceIds')
            ->with('expression_language')
            ->willReturn([]);

        $definition
            ->expects($this->once())
            ->method('addMethodCall')
            ->with('addCatchall', [new Reference('Expression.Listener')]);

        $subject->process($container);
    }
}