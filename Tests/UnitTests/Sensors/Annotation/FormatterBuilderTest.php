<?php

namespace Tests\Homie\Sensors\Command;

use Doctrine\Common\Annotations\Reader;
use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\CompilerPass\Annotation\FormatterBuilder;
use Homie\Sensors\CompilerPass\SensorFormatter as CompilerPass;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Definition;

class FormatterBuilderTest extends TestCase
{

    public function testBuild()
    {
        /** @var Reader $reader */
        $reader = $this->getMock(Reader::class);

        $subject = new FormatterBuilder($reader);

        $data = [];
        $annotation = new Sensor($data);
        $annotation->name = 'name';

        /** @var ReflectionClass|MockObject $class */
        $class = $this->getMock(ReflectionClass::class, [], [], '', false);
        $class->expects($this->once())
            ->method('getMethods')
            ->willReturn([]);

        $data = $subject->build($class, $annotation);

        $definition = new Definition();
        $definition->setPublic(false);
        $definition->addTag(CompilerPass::TAG);
        $expected = [
            'name',
            $definition
        ];

        $this->assertEquals($expected, $data);
    }
}
