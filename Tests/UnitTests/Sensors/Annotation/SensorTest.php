<?php

namespace Tests\Homie\Sensors\Annotation;

use Doctrine\Common\Annotations\Reader;
use Homie\Sensors\Annotation\SensorBuilder;
use Homie\Sensors\Annotation\Sensor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SensorTest extends TestCase
{

    public function testBuild()
    {
        /** @var Reader $reader */
        $reader = $this->createMock(Reader::class);

        /** @var ContainerBuilder $container */
        $container = $this->createMock(ContainerBuilder::class);

        $subject = new Sensor([]);
        $actual  = $subject->getBuilder($container, $reader);

        $this->assertInstanceOf(SensorBuilder::class, $actual);
    }
}
