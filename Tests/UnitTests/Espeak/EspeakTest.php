<?php

namespace Tests\Homie\Espeak;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Espeak\Espeak;
use Homie\Client\Adapter\LocalClient;

/**
 * @covers \Homie\Espeak\Espeak
 */
class EspeakTest extends TestCase
{

    /**
     * @var Espeak
     */
    private $subject;

    /**
     * @var LocalClient|MockObject
     */
    private $client;

    public function setUp()
    {
        $this->client  = $this->createMock(LocalClient::class);

        $this->subject = new Espeak($this->client, 'espeak');
    }

    public function testSpeakWithEmptyText()
    {
        $text    = '';
        $volume  = 100;
        $speed   = 105;
        $speaker = 'de';

        $this->client
            ->expects($this->never())
            ->method('execute');

        $this->subject->speak($text, $volume, $speed, $speaker);
    }

    public function testSpeak()
    {
        $text    = 'text';
        $volume  = 100;
        $speed   = 105;
        $speaker = 'de';

        $this->client
            ->expects($this->once())
            ->method('execute');

        $this->subject->speak($text, $volume, $speed, $speaker);
    }
}
