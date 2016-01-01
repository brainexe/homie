<?php

namespace Tests\Homie\Media;

use Homie\Client\ClientInterface;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Media\Sound;

/**
 * @covers Homie\Media\Sound
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
        $this->client  = $this->getMock(ClientInterface::class, [], [], '', false);
        $this->subject = new Sound($this->client);
    }

    public function testPlaySound()
    {
        $file = 'file';

        $this->client
            ->expects($this->once())
            ->method('execute')
            ->with("mplayer", [Sound::ROOT . 'file']);

        $this->subject->playSound($file);
    }
}
