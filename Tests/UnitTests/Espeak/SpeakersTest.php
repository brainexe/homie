<?php

namespace Tests\Homie\Espeak;

use Homie\Espeak\Speakers;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Espeak\Espeak;
use Homie\Client\Adapter\LocalClient;
use RuntimeException;

/**
 * @covers Homie\Espeak\Speakers
 */
class SpeakersTest extends TestCase
{

    /**
     * @var Speakers
     */
    private $subject;

    /**
     * @var LocalClient|MockObject
     */
    private $client;

    public function setUp()
    {
        $this->client  = $this->createMock(LocalClient::class);

        $this->subject = new Speakers($this->client, 'espeak', ['de', 'en']);
    }

    public function testSpeak()
    {
        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->with('espeak', ['--voices'])
            ->willReturn(file_get_contents(__DIR__ . '/espeak_voices.txt'));

        $expected = [
            'de'    => 'German - M',
            'en'    => 'Default - M',
            'en-gb' => 'English - M',
            'en-sc' => 'En-scottish - M',
            'en-us' => 'English-us - M',
            'en-wi' => 'En-westindies - M',
        ];

        $actual  = $this->subject->getSpeakers();

        $this->assertEquals($expected, iterator_to_array($actual));
    }

    public function testSpeakWithException()
    {
        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->with('espeak', ['--voices'])
            ->willThrowException(new RuntimeException());

        $actual  = $this->subject->getSpeakers();

        $this->assertEquals([], iterator_to_array($actual));
    }
}
