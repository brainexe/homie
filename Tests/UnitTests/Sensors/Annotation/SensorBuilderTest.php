<?php

namespace Tests\Homie\Sensors\Command;

use Doctrine\Common\Annotations\Reader;
use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Annotation\SensorBuilder;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Definition;
use Homie\Sensors\CompilerPass\Sensor as CompilerPass;

class SensorBuilderTest extends TestCase
{

    public function testBuild()
    {
        /** @var Reader $reader */
        $reader = $this->createMock(Reader::class);

        $subject = new SensorBuilder($reader);

        $data = [];
        $annotation = new Sensor($data);
        $annotation->name = 'name';

        /** @var ReflectionClass|MockObject $class */
        $class = $this->createMock(ReflectionClass::class);
        $class->expects($this->once())
            ->method('getMethods')
            ->willReturn([]);

        $data = $subject->build($class, $annotation);

        $definition = new Definition();
        $definition->setPublic(true);
        $definition->setShared(false);
        $definition->addTag(CompilerPass::TAG);
        $expected = [
            '__name',
            $definition
        ];

        $this->assertEquals($expected, $data);
    }
}
