<?php

namespace Tests\Homie\Sensors\Sensors\Misc;

use GuzzleHttp\Client;
use Homie\Sensors\Definition;
use Homie\Sensors\Sensors\Misc\Metawear;
use Homie\Sensors\SensorVO;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Psr\Http\Message\ResponseInterface;

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
     * @var Client|MockObject
     */
    private $client;

    /**
     * @var string
     */
    private $url;

    public function setUp()
    {
        $this->client  = $this->createMock(Client::class);

        $this->url = 'http://metawear:8081';

        $this->subject = new Metawear(
            $this->client,
            $this->url
        );
    }

    public function testGetValue()
    {
        $sensor = new SensorVO();
        $sensor->parameter = 'temperature';

        $response = $this->createMock(ResponseInterface::class);

        $response
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        $response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn('12.1');

        $this->client
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'http://metawear:8081/temperature/')
            ->willReturn($response);

        $actual = $this->subject->getValue($sensor);

        $this->assertEquals(12.1, $actual);
    }

    /**
     * @expectedException \Homie\Sensors\Exception\InvalidSensorValueException
     * @expectedExceptionMessage Invalid metawear response: 12.1
     */
    public function testGetValueWithError()
    {
        $sensor = new SensorVO();
        $sensor->parameter = 'temperature';

        $response = $this->createMock(ResponseInterface::class);

        $response
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(500);

        $response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn('12.1');

        $this->client
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'http://metawear:8081/temperature/')
            ->willReturn($response);

        $this->subject->getValue($sensor);
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
