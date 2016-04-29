<?php

namespace Tests\Homie\Sensors\Controller;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\Sensors\Controller\Administration;
use Homie\Sensors\Exception\InvalidSensorValueException;
use Homie\Sensors\GetValue\GetSensorValueEvent;
use Homie\Sensors\Interfaces\Parameterized;
use Homie\Sensors\Interfaces\Sensor;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Sensors\SensorVO;
use Homie\Sensors\Builder;
use Symfony\Component\HttpFoundation\Request;
use Homie\Sensors\SensorGateway;
use Homie\Sensors\SensorBuilder;

/**
 * @covers Homie\Sensors\Controller\Administration
 */
class AdministrationTest extends TestCase
{

    /**
     * @var Administration
     */
    private $subject;

    /**
     * @var SensorGateway|MockObject
     */
    private $gateway;

    /**
     * @var SensorBuilder|MockObject
     */
    private $builder;

    /**
     * @var Builder|MockObject
     */
    private $voBuilder;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->gateway       = $this->getMock(SensorGateway::class, [], [], '', false);
        $this->builder       = $this->getMock(SensorBuilder::class, [], [], '', false);
        $this->voBuilder     = $this->getMock(Builder::class, [], [], '', false);
        $this->dispatcher    = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new Administration(
            $this->gateway,
            $this->builder,
            $this->voBuilder
        );

        $this->subject->setEventDispatcher($this->dispatcher);
    }


    public function testAddSensor()
    {
        $type        = 'type';
        $name        = 'name';
        $description = 'description';
        $parameter   = 'parameter';
        $interval    = 12;
        $node        = 1;

        $request = new Request();
        $request->request->set('type', $type);
        $request->request->set('name', $name);
        $request->request->set('description', $description);
        $request->request->set('parameter', $parameter);
        $request->request->set('interval', $interval);
        $request->request->set('node', $node);

        $sensorVo = new SensorVO();
        $sensorVo->name = $name;

        $this->voBuilder
            ->expects($this->once())
            ->method('build')
            ->with(
                null,
                $name,
                $description,
                $interval,
                $node,
                $parameter,
                $type
            )
            ->willReturn($sensorVo);
        $this->gateway
            ->expects($this->once())
            ->method('addSensor')
            ->with($sensorVo);

        $event = new GetSensorValueEvent($sensorVo);
        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event);

        $actual = $this->subject->addSensor($request);
        $this->assertEquals($sensorVo, $actual);
    }

    public function testEdit()
    {
        $sensorId = 12;

        $request = new Request();

        $sensorRaw = ['raw'];
        $sensorVo = new SensorVO();
        $this->gateway
            ->expects($this->once())
            ->method('getSensor')
            ->with($sensorId)
            ->willReturn($sensorRaw);
        $this->voBuilder
            ->expects($this->once())
            ->method('buildFromArray')
            ->with($sensorRaw)
            ->willReturn($sensorVo);

        $expected = new SensorVO();
        $this->gateway
            ->expects($this->once())
            ->method('save')
            ->with($expected);

        $this->subject->edit($request, $sensorId);
    }


    public function testDelete()
    {
        $sensorId = 12;
        $request = new Request();

        $this->gateway
            ->expects($this->once())
            ->method('deleteSensor')
            ->willReturn($sensorId);

        $actualResult = $this->subject->delete($request, $sensorId);

        $this->assertTrue($actualResult);
    }

    public function testParametersWithoutParameter()
    {
        $sensorType = 'myType';
        $request = new Request();

        $sensor = $this->getMock(Sensor::class);

        $this->builder
            ->expects($this->once())
            ->method('build')
            ->with($sensorType)
            ->willReturn($sensor);

        $actual = $this->subject->parameters($request, $sensorType);

        $this->assertFalse($actual);
    }

    public function testParametersWithoutSearch()
    {
        $sensorType = 'myType';
        $request = new Request();

        $sensor = $this->getMock(Parameterized::class);

        $this->builder
            ->expects($this->once())
            ->method('build')
            ->with($sensorType)
            ->willReturn($sensor);

        $actual = $this->subject->parameters($request, $sensorType);

        $this->assertTrue($actual);
    }

    public function testIsValid()
    {
        $sensorType = 'myType';
        $parameter  = 'myParameter';
        $request = new Request();

        $sensor = $this->getMock(Parameterized::class);

        $sensorVo = new SensorVO();
        $sensorVo->parameter = $parameter;

        $this->builder
            ->expects($this->once())
            ->method('build')
            ->with($sensorType)
            ->willReturn($sensor);

        $sensor
            ->expects($this->once())
            ->method('isSupported')
            ->with($sensorVo)
            ->willReturn(true);

        $actual = $this->subject->isValid($request, $sensorType, $parameter);

        $expected = [
            'isValid' => true,
            'message' => ''
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testIsValidWithException()
    {
        $sensorType = 'myType';
        $parameter  = 'myParameter';
        $request = new Request();

        $sensor = $this->getMock(Parameterized::class);

        $sensorVo = new SensorVO();
        $sensorVo->parameter = $parameter;

        $this->builder
            ->expects($this->once())
            ->method('build')
            ->with($sensorType)
            ->willReturn($sensor);

        $sensor
            ->expects($this->once())
            ->method('isSupported')
            ->with($sensorVo)
            ->willThrowException(new InvalidSensorValueException($sensorVo, 'invalid sensor'));

        $actual = $this->subject->isValid($request, $sensorType, $parameter);

        $expected = [
            'isValid' => false,
            'message' => 'invalid sensor'
        ];
        $this->assertEquals($expected, $actual);
    }
}
