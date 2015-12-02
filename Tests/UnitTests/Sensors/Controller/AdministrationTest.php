<?php

namespace Tests\Homie\Sensors\Controller;

use Homie\Sensors\Controller\Administration;
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

    public function setUp()
    {
        $this->gateway       = $this->getMock(SensorGateway::class, [], [], '', false);
        $this->builder       = $this->getMock(SensorBuilder::class, [], [], '', false);
        $this->voBuilder     = $this->getMock(Builder::class, [], [], '', false);

        $this->subject = new Administration(
            $this->gateway,
            $this->builder,
            $this->voBuilder
        );
    }


    public function testAddSensor()
    {
        $type        = 'type';
        $name        = 'name';
        $description = 'descritpion';
        $pin         = 'pin';
        $interval    = 12;
        $node        = 1;

        $request = new Request();
        $request->request->set('type', $type);
        $request->request->set('name', $name);
        $request->request->set('description', $description);
        $request->request->set('pin', $pin);
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
                $pin,
                $type
            )
            ->willReturn($sensorVo);
        $this->gateway
            ->expects($this->once())
            ->method('addSensor')
            ->with($sensorVo);

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

}