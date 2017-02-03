<?php

namespace Tests\Homie\Sensors\CompilerPass\Annotation;

use Doctrine\Common\Annotations\Reader;
use Homie\Sensors\Annotation\SensorBuilder;
use Homie\Sensors\Annotation\Sensor;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SensorTest extends TestCase
{
    public function testBuild()
    {
        /** @var Reader $reader */
        $reader = $this->createMock(Reader::class);

        /** @var ContainerBuilder $container */
        $container = $this->createMock(ContainerBuilder::class);

        $annotation = new Sensor([]);
        $builder = $annotation::getBuilder($container, $reader);

        $this->assertInstanceOf(SensorBuilder::class, $builder);
    }
}
