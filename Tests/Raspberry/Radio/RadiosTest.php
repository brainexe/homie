<?php

namespace Raspberry\Tests\Radio;

use BrainExe\Core\Application\UserException;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Raspberry\Radio\RadioGateway;
use Raspberry\Radio\Radios;
use Raspberry\Radio\VO\RadioVO;

class RadiosTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Radios
     */
    private $subject;

    /**
     * @var RadioGateway|MockObject
     */
    private $mockRadioGateway;

    public function setUp()
    {
        $this->mockRadioGateway = $this->getMock(RadioGateway::class);

        $this->subject = new Radios($this->mockRadioGateway);
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
            'radioId' => 1,
            'name' => 'test',
            'description' => 'description',
            'pin' => 100,
            'code' => 1
        ];

        $this->mockRadioGateway
            ->expects($this->once())
            ->method('getRadios')
            ->willReturn([$radio]);

        $actualResult = $this->subject->getRadios();

        $expected              = new RadioVO();
        $expected->radioId     = $radio['radioId'];
        $expected->name        = $radio['name'];
        $expected->description = $radio['description'];
        $expected->pin         = $radio['pin'];
        $expected->code        = $radio['code'];

        $this->assertEquals([$radio['radioId'] => $expected], $actualResult);
    }

    public function testAddRadio()
    {
        $radioVo = new RadioVO();
        $radioVo->name = 'foo';
        $radioVo->description = 'foo extended';
        $radioVo->code = '1101';
        $radioVo->pin = 1;

        $radioId = 12;

        $this->mockRadioGateway
            ->expects($this->once())
            ->method('addRadio')
            ->with($radioVo)
            ->willReturn($radioId);

        $actualResult = $this->subject->addRadio($radioVo);

        $this->assertEquals($radioId, $actualResult);
    }

    public function testDeleteRadio()
    {
        $radioId = 12;

        $this->mockRadioGateway
            ->expects($this->once())
            ->method('deleteRadio')
            ->with($radioId);

        $this->subject->deleteRadio($radioId);
    }

    public function testGetRadio()
    {
        $radioId = 21;

        $radio = [
            'radioId' => $radioId,
            'name' => 'test',
            'description' => 'description',
            'pin' => 100,
            'code' => 1
        ];

        $this->mockRadioGateway
            ->expects($this->once())
            ->method('getRadio')
            ->with($radioId)
            ->willReturn($radio);

        $result = $this->subject->getRadio($radioId);

        $radioVo              = new RadioVO();
        $radioVo->radioId     = $radioId;
        $radioVo->name        = $radio['name'];
        $radioVo->description = $radio['description'];
        $radioVo->code        = $radio['code'];
        $radioVo->pin         = $radio['pin'];

        $this->assertEquals($radioVo, $result);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid radio: 4
     */
    public function testGetInvalidRadio()
    {
        $radioId = 4;

        $radio = [];

        $this->mockRadioGateway
            ->expects($this->once())
            ->method('getRadio')
            ->with($radioId)
            ->willReturn($radio);

        $this->subject->getRadio($radioId);
    }

    /**
     * @return array[]
     */
    public static function providerPins()
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
