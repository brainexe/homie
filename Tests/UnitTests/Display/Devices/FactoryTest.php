<?php

namespace Tests\Homie\Display\Devices;

use Homie\Display\Devices\DeviceInterface;
use Homie\Display\Devices\Factory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Homie\Display\Devices\Factory
 */
class FactoryTest extends TestCase
{

    public function testAll()
    {
        /** @var DeviceInterface $device */
        $device = $this->createMock(DeviceInterface::class);

        $subject = new Factory(['test' => $device]);

        $this->assertEquals(['test' => $device], $subject->getAll());
        $this->assertEquals($device, $subject->getDevice('test'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid device test2
     */
    public function testGetInvalid()
    {
        /** @var DeviceInterface $device */
        $device = $this->createMock(DeviceInterface::class);

        $subject = new Factory(['test' => $device]);

        $this->assertEquals($device, $subject->getDevice('test2'));
    }
}
