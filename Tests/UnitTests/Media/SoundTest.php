<?php

namespace Tests\Homie\Media;

use Homie\Client\ClientInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Media\Sound;

/**
 * @covers \Homie\Media\Sound
 */
class SoundTest extends TestCase
{

    /**
     * @var Sound
     */
    private $subject;

    /**
     * @var ClientInterface|MockObject
     */
    private $client;

    public function setUp()
    {
        $this->client  = $this->createMock(ClientInterface::class);
        $this->subject = new Sound($this->client, 'mplayer');
    }

    public function testPlaySound()
    {
        $file = 'file';

        $this->client
            ->expects($this->once())
            ->method('execute')
            ->with("mplayer", [Sound::DIRECTORY . 'file']);

        $this->subject->playSound($file);
    }
}
