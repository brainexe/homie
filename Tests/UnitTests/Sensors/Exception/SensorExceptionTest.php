<?php

namespace Tests\Homie\Sensors\Exception;

use Homie\Sensors\Exception\SensorException;
use Homie\Sensors\SensorVO;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Homie\Sensors\Exception\SensorException
 */
class SensorExceptionTest extends TestCase
{

    public function testException()
    {
        $sensor = new SensorVO();

        $subject = new SensorException($sensor, 'message');

        $this->assertEquals('message', $subject->getMessage());
        $this->assertEquals($sensor, $subject->getSensor());
    }
}
