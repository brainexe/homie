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
     * @param string $input_pin
     * @param string $expected_pin
     * @throws UserException
     */
    public function testGetRadioPin($input_pin, $expected_pin)
    {
        if (false === $expected_pin) {
            $this->setExpectedException(UserException::class);
        }
        $actual_pin = $this->subject->getRadioPin($input_pin);

        $this->assertEquals($expected_pin, $actual_pin);
    }

    public function testGetRadios()
    {
        $radio = [
        'id' => 1,
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

        $expected = new RadioVO();
        $expected->radioId = $radio['id'];
        $expected->name = $radio['name'];
        $expected->description = $radio['description'];
        $expected->pin = $radio['pin'];
        $expected->code = $radio['code'];

        $this->assertEquals([$radio['id'] => $expected], $actualResult);
    }

    public function testAddRadio()
    {
        $radio_vo = new RadioVO();
        $radio_vo->name = 'foo';
        $radio_vo->description = 'foo extended';
        $radio_vo->code = '1101';
        $radio_vo->pin = 1;

        $radio_id = 12;

        $this->mockRadioGateway
            ->expects($this->once())
            ->method('addRadio')
            ->with($radio_vo)
            ->willReturn($radio_id);

        $actualResult = $this->subject->addRadio($radio_vo);

        $this->assertEquals($radio_id, $actualResult);
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
            'id' => $radioId,
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

        $radio_vo              = new RadioVO();
        $radio_vo->radioId     = $radioId;
        $radio_vo->name        = $radio['name'];
        $radio_vo->description = $radio['description'];
        $radio_vo->code        = $radio['code'];
        $radio_vo->pin         = $radio['pin'];

        $this->assertEquals($radio_vo, $result);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid radio: 4
     */
    public function testGetInvalidRadio()
    {
        $radio_id = 4;

        $radio = [];

        $this->mockRadioGateway
            ->expects($this->once())
            ->method('getRadio')
            ->with($radio_id)
            ->willReturn($radio);

        $this->subject->getRadio($radio_id);
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
