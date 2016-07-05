<?php

namespace Tests\Homie\Switches\Controller;

use ArrayIterator;
use Homie\Switches\VO\ArduinoSwitchVO;
use Homie\Switches\VO\GpioSwitchVO;
use Homie\Switches\VO\ParticleVO;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Switches\Controller\Controller;
use Homie\Switches\VO\RadioVO;
use Symfony\Component\HttpFoundation\Request;
use Homie\Switches\Switches;
use BrainExe\Core\EventDispatcher\EventDispatcher;

/**
 * @covers Homie\Switches\Controller\Controller
 */
class ControllerTest extends TestCase
{

    /**
     * @var Controller
     */
    private $subject;

    /**
     * @var Switches|MockObject
     */
    private $switches;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->switches   = $this->createMock(Switches::class);
        $this->dispatcher = $this->createMock(EventDispatcher::class);

        $this->subject = new Controller($this->switches);
    }

    public function testIndex()
    {
        $radiosFormatted = ['radios_formatted'];

        $this->switches
            ->expects($this->once())
            ->method('getAll')
            ->willReturn(new ArrayIterator($radiosFormatted));

        $actual = $this->subject->index();

        $expected = [
            'switches'  => $radiosFormatted,
            'radioPins' => Switches::RADIO_PINS,
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testAddRadio()
    {
        $name        = 'name';
        $description = 'description';
        $code        = 12;
        $pinRaw      = 'A';
        $pin         = 1;

        $request = new Request();
        $request->request->set('name', $name);
        $request->request->set('description', $description);
        $request->request->set('code', $code);
        $request->request->set('pin', $pinRaw);
        $request->request->set('type', RadioVO::TYPE);

        $radioVo = new RadioVO();
        $radioVo->name        = $name;
        $radioVo->description = $description;
        $radioVo->code        = $code;
        $radioVo->pin         = $pin;

        $this->switches
            ->expects($this->once())
            ->method('add')
            ->with($radioVo);

        $this->switches
            ->expects($this->once())
            ->method('getRadioPin')
            ->with($pinRaw)
            ->willReturn($pin);

        $actual = $this->subject->add($request);

        $this->assertEquals($radioVo, $actual);
    }

    public function testAddGpio()
    {
        $name        = 'name';
        $description = 'description';
        $pin         = 1;

        $request = new Request();
        $request->request->set('name', $name);
        $request->request->set('description', $description);
        $request->request->set('pin', $pin);
        $request->request->set('type', GpioSwitchVO::TYPE);

        $switch = new GpioSwitchVO();
        $switch->name        = $name;
        $switch->description = $description;
        $switch->pin         = $pin;

        $this->switches
            ->expects($this->once())
            ->method('add')
            ->with($switch);

        $actual = $this->subject->add($request);

        $this->assertEquals($switch, $actual);
    }

    public function testAddArduino()
    {
        $name        = 'name';
        $description = 'description';
        $pin         = 1;
        $node        = 42;

        $request = new Request();
        $request->request->set('name', $name);
        $request->request->set('description', $description);
        $request->request->set('pin', $pin);
        $request->request->set('nodeId', $node);
        $request->request->set('type', ArduinoSwitchVO::TYPE);

        $switch = new ArduinoSwitchVO();
        $switch->name        = $name;
        $switch->description = $description;
        $switch->pin         = $pin;
        $switch->nodeId      = $node;

        $this->switches
            ->expects($this->once())
            ->method('add')
            ->with($switch);

        $actual = $this->subject->add($request);

        $this->assertEquals($switch, $actual);
    }

    public function testAddParticle()
    {
        $name        = 'name';
        $description = 'description';
        $function    = 'myFunction';
        $node        = 42;

        $request = new Request();
        $request->request->set('name', $name);
        $request->request->set('description', $description);
        $request->request->set('function', $function);
        $request->request->set('nodeId', $node);
        $request->request->set('type', ParticleVO::TYPE);

        $switch = new ParticleVO();
        $switch->name        = $name;
        $switch->description = $description;
        $switch->function    = $function;
        $switch->nodeId      = $node;

        $this->switches
            ->expects($this->once())
            ->method('add')
            ->with($switch);

        $actual = $this->subject->add($request);

        $this->assertEquals($switch, $actual);
    }

    /**
     * @expectedException \BrainExe\Core\Application\UserException
     * @expectedExceptionMessage Invalid switch type: foo
     */
    public function testAddInvalid()
    {
        $request = new Request();
        $request->request->set('type', 'foo');

        $this->subject->add($request);
    }

    public function testDeleteRadio()
    {
        $request = new Request();
        $switchId = 10;

        $this->switches
            ->expects($this->once())
            ->method('delete')
            ->with($switchId);

        $actual = $this->subject->delete($request, $switchId);

        $this->assertTrue($actual);
    }
}
