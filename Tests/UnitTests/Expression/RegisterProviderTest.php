<?php

namespace Tests\Homie\Expression;

use Homie\Expression\CompilerPass\RegisterProvider;
use Homie\Expression\Language;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class RegisterProviderTest extends TestCase
{

    public function testProcess()
    {
        $subject = new RegisterProvider();

        /** @var ContainerBuilder|MockObject $container */
        $container  = $this->createMock(ContainerBuilder::class);
        $definition = $this->createMock(Definition::class);
        $language   = $this->createMock(Definition::class);

        $container
            ->expects($this->at(0))
            ->method('findDefinition')
            ->with('EventDispatcher')
            ->willReturn($definition);
        $container
            ->expects($this->at(1))
            ->method('findDefinition')
            ->with(Language::class)
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

        $language
            ->expects($this->once())
            ->method('setArguments')
            ->with([new Reference('service_container')]);

        $subject->process($container);
    }
}
