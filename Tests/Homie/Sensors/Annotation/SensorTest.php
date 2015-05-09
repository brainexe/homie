<?php

namespace Tests\Homie\Sensors\Annotation;

use Doctrine\Common\Annotations\Reader;
use Homie\Sensors\Annotation\Builder;
use Homie\Sensors\Annotation\Sensor;
use PHPUnit_Framework_TestCase as TestCase;

class SensorTest extends TestCase
{

    public function testBuild()
    {
        /** @var Reader $reader */
        $reader = $this->getMock(Reader::class);

        $subject = new Sensor([]);
        $actual  = $subject->getBuilder($reader);

        $this->assertInstanceOf(Builder::class, $actual);
    }
}
