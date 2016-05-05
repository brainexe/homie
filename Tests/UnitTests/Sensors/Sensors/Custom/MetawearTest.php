<?php

namespace Tests\Homie\Sensors\Sensors\Misc;

use Homie\Client\Adapter\LocalClient;
use Homie\Sensors\Definition;
use Homie\Sensors\Sensors\Misc\Metawear;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * @covers Homie\Sensors\Sensors\Misc\Metawear
 */
class MetawearTest extends TestCase
{

    /**
     * @var Metawear
     */
    private $subject;

    /**
     * @var LocalClient|MockObject
     */
    private $client;

    /**
     * @var string
     */
    private $url;

    public function setUp()
    {
        $this->client  = $this->getMock(LocalClient::class, [], [], '', false);

        $this->url = 'http://metawear:8081';
        
        $this->subject = new Metawear(
            $this->client,
            $this->url
        );
    }

    public function testGetDefinition()
    {
        $actual = $this->subject->getDefinition();
        $this->assertInstanceOf(Definition::class, $actual);
    }

    public function testSearch()
    {
        $actual = $this->subject->search();
        $this->assertInternalType('array', $actual);
    }
}
