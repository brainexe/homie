<?php

namespace Tests\Homie\Sensors\Command;

use Doctrine\Common\Annotations\Reader;
use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Annotation\SensorBuilder;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Homie\Sensors\CompilerPass\Sensor as CompilerPass;

class SensorBuilderTest extends TestCase
{

    public function testBuild()
    {
        /** @var Reader $reader */
        $reader = $this->createMock(Reader::class);

        /** @var ContainerBuilder $container */
        $container = $this->createMock(ContainerBuilder::class);

        $subject = new SensorBuilder($container, $reader);

        $data = [];
        $annotation = new Sensor($data);
        $annotation->name = 'name';

        /** @var ReflectionClass|MockObject $class */
        $class = $this->createMock(ReflectionClass::class);
        $class->expects($this->once())
            ->method('getMethods')
            ->willReturn([]);

        $definition = new Definition();

        $data = $subject->build($class, $annotation, $definition);

        $definition->setPublic(true);
        $definition->setShared(false);
        $definition->addTag(CompilerPass::TAG);
        $expected = [
            'name',
            $definition
        ];

        $this->assertEquals($expected, $data);
    }
}
