<?php

namespace Tests\Homie\Sensors\CompilerPass\Annotation;

use Doctrine\Common\Annotations\Reader;
use Homie\Sensors\CompilerPass\Annotation\FormatterBuilder;
use Homie\Sensors\CompilerPass\Annotation\SensorFormatter;
use PHPUnit_Framework_TestCase as TestCase;

class SensorFormatterTest extends TestCase
{
    public function testBuild()
    {
        /** @var Reader $reader */
        $reader = $this->createMock(Reader::class);

        $annotation = new SensorFormatter([]);
        $builder = $annotation::getBuilder($reader);

        $this->assertInstanceOf(FormatterBuilder::class, $builder);
    }
}
