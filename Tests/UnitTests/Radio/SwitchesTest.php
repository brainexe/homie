<?php

namespace Homie\Tests\Radio;

use BrainExe\Core\Application\UserException;
use Homie\Radio\VO\GpioSwitchVO;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Homie\Radio\Gateway;
use Homie\Radio\Switches;
use Homie\Radio\VO\RadioVO;

class SwitchesTest extends TestCase
{

    /**
     * @var Switches
     */
    private $subject;

    /**
     * @var Gateway|MockObject
     */
    private $gateway;

    public function setUp()
    {
        $this->gateway = $this->getMock(Gateway::class);

        $this->subject = new Switches($this->gateway);
    }

    /**
     * @dataProvider providerPins
     * @param string $inputPin
     * @param string $expectedPin
     * @throws UserException
     */
    public function testGetRadioPin($inputPin, $expectedPin)
    {
        if (false === $expectedPin) {
            $this->setExpectedException(UserException::class);
        }
        $actualPin = $this->subject->getRadioPin($inputPin);

        $this->assertEquals($expectedPin, $actualPin);
    }

    public function testGetRadios()
    {
        $radio = [
            'switchId' => 1,
            'name' => 'test',
            'description' => 'description',
            'pin' => 100,
            'type' => GpioSwitchVO::TYPE,
        ];

        $this->gateway
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([$radio]);

        $actual = $this->subject->getAll();

        $expected              = new GpioSwitchVO();
        $expected->switchId    = $radio['switchId'];
        $expected->name        = $radio['name'];
        $expected->description = $radio['description'];
        $expected->pin         = $radio['pin'];

        $this->assertEquals([$radio['switchId'] => $expected], iterator_to_array($actual));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Invalid switch type: invalid
     */
    public function testGetInvalidRadio()
    {
        $switch = [
            'switchId' => 1,
            'name' => 'test',
            'description' => 'description',
            'pin' => 100,
            'type' => 'invalid'
        ];

        $this->gateway
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([$switch]);

        iterator_to_array($this->subject->getAll());
    }

    public function testAddRadio()
    {
        $radioVo = new RadioVO();
        $radioVo->name = 'foo';
        $radioVo->description = 'foo extended';
        $radioVo->code = '1101';
        $radioVo->pin = 1;

        $radioId = 12;

        $this->gateway
            ->expects($this->once())
            ->method('add')
            ->with($radioVo)
            ->willReturn($radioId);

        $actualResult = $this->subject->add($radioVo);

        $this->assertEquals($radioId, $actualResult);
    }

    public function testDeleteRadio()
    {
        $radioId = 12;

        $this->gateway
            ->expects($this->once())
            ->method('delete')
            ->with($radioId);

        $this->subject->delete($radioId);
    }

    public function testGetRadio()
    {
        $switchId = 21;

        $radio = [
            'switchId' => $switchId,
            'name' => 'test',
            'description' => 'description',
            'pin' => 100,
            'code' => 1,
            'type' => RadioVO::TYPE
        ];

        $this->gateway
            ->expects($this->once())
            ->method('get')
            ->with($switchId)
            ->willReturn($radio);

        $result = $this->subject->get($switchId);

        $radioVo              = new RadioVO();
        $radioVo->switchId    = $switchId;
        $radioVo->name        = $radio['name'];
        $radioVo->description = $radio['description'];
        $radioVo->code        = $radio['code'];
        $radioVo->pin         = $radio['pin'];

        $this->assertEquals($radioVo, $result);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid switch: 4
     */
    public function testGetEmptyRadio()
    {
        $radioId = 4;

        $radio = [];

        $this->gateway
            ->expects($this->once())
            ->method('get')
            ->with($radioId)
            ->willReturn($radio);

        $this->subject->get($radioId);
    }

    /**
     * @return array[]
     */
    public function providerPins()
    {
        return [
            [1, 1],
            [2, 2],
            ["2", 2],
            [0, false],
            [0.5, false],
            ['A', 1],
            ['D', 4],
            ['', false],
            ['G', false],
        ];
    }
}
