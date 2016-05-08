<?php

namespace Tests\Homie\Espeak\Controller;

use ArrayIterator;
use Homie\Espeak\Controller\Speakers;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Espeak\Speakers as SpeakersModel;

/**
 * @covers Homie\Espeak\Controller\Speakers
 */
class SpeakersTest extends TestCase
{

    /**
     * @var Speakers
     */
    private $subject;

    /**
     * @var SpeakersModel|MockObject
     */
    private $speakers;

    public function setUp()
    {
        $this->speakers = $this->getMockWithoutInvokingTheOriginalConstructor(SpeakersModel::class);

        $this->subject = new Speakers($this->speakers);
    }

    public function testSpeakers()
    {
        $speakers = new ArrayIterator([]);

        $this->speakers
            ->expects($this->once())
            ->method('getSpeakers')
            ->willReturn($speakers);

        $actual = $this->subject->speakers();

        $this->assertEquals($speakers, $actual);
    }
}
