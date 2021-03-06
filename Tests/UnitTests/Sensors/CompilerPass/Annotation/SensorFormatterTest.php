<?php

namespace Tests\Homie\Sensors\CompilerPass\Annotation;

use Doctrine\Common\Annotations\Reader;
use Homie\Sensors\CompilerPass\Annotation\FormatterBuilder;
use Homie\Sensors\CompilerPass\Annotation\SensorFormatter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SensorFormatterTest extends TestCase
{
    public function testBuild()
    {
        /** @var Reader $reader */
        $reader = $this->createMock(Reader::class);
        /** @var ContainerBuilder $container */
        $container = $this->createMock(ContainerBuilder::class);

        $annotation = new SensorFormatter([]);
        $builder = $annotation::getBuilder($container, $reader);

        $this->assertInstanceOf(FormatterBuilder::class, $builder);
    }
}
