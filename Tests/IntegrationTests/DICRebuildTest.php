<?php

namespace IntegrationTests;

use BrainExe\Core\DependencyInjection\Rebuild;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DICRebuildTest extends TestCase
{

    public function testRebuildContainer()
    {
        $subject = new Rebuild();

        $actualResult = $subject->rebuildDIC(false);

        $this->assertInstanceOf(ContainerBuilder::class, $actualResult);
    }
}
