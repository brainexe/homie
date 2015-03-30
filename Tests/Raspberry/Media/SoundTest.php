<?php

namespace Tests\Raspberry\Media;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Media\Sound;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @covers Raspberry\Media\Sound
 */
class SoundTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Sound
     */
    private $subject;

    /**
     * @var ProcessBuilder|MockObject
     */
    private $mockProcessBuilder;

    public function setUp()
    {
        $this->mockProcessBuilder = $this->getMock(ProcessBuilder::class, [], [], '', false);
        $this->subject = new Sound($this->mockProcessBuilder);
    }

    public function testPlaySound()
    {
        $file = 'file';

        $process = $this->getMock(Process::class, [], [], '', false);

        $this->mockProcessBuilder
            ->expects($this->once())
            ->method('add')
            ->willReturn($this->mockProcessBuilder);

        $this->mockProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($process);

        $process
            ->expects($this->once())
            ->method('setCommandLine')
            ->with(sprintf(Sound::COMMAND, $file))
            ->willReturnSelf();

        $process
            ->expects($this->once())
            ->method('run');

        $this->subject->playSound($file);
    }
}
