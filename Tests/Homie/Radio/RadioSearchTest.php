<?php

namespace Tests\Homie\Radio;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Homie\Radio\RadioSearch;
use Homie\Radio\Radios;
use Homie\Radio\VO\RadioVO;

/**
 * @covers Homie\Radio\RadioSearch
 */
class RadioSearchTest extends TestCase
{

    /**
     * @var RadioSearch
     */
    private $subject;

    /**
     * @var Radios|MockObject
     */
    private $radios;

    public function setUp()
    {
        $this->radios  = $this->getMock(Radios::class, [], [], '', false);
        $this->subject = new RadioSearch($this->radios);
    }

    public function testGetDataEmpty()
    {
        $radios = [];

        $this->radios
            ->expects($this->once())
            ->method('getRadios')
            ->willReturn($radios);

        $actualResult = $this->subject->getData();
        $expectedResult = [];

        $this->assertEquals($expectedResult, $actualResult);
    }
    public function testGetData()
    {
        $radio = new RadioVO();
        $radio->name = $name = 'radio name';

        $radios = [$radio];

        $this->radios
            ->expects($this->once())
            ->method('getRadios')
            ->willReturn($radios);

        $actualResult = $this->subject->getData();
        $expectedResult = [
            [
                'body'  => [
                    'name' => $name
                ],
                'index' => 'radio',
                'type'  => 'radio',
                'id'    => null,
            ]
        ];

        $this->assertEquals($expectedResult, $actualResult);
    }
}
