<?php

namespace Tests\Homie\Sensors;

use Homie\Sensors\SensorValueEvent;
use Homie\Sensors\SensorVO;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Homie\Sensors\SensorValueEvent
 */
class SensorValueEventTest extends TestCase
{

    public function testProperties()
    {
        $sensorVo       = new SensorVO();
        $value          = 'value';
        $valueFormatted = 'valueFormatted';
        $timestamp      = 12345;
        $eventName      = 'eventName';

        $subject = new SensorValueEvent(
            $eventName,
            $sensorVo,
            $value,
            $valueFormatted,
            $timestamp
        );

        $this->assertEquals($eventName, $subject->getEventName());
        $this->assertEquals($sensorVo, $subject->sensorVo);
        $this->assertEquals($value, $subject->value);
        $this->assertEquals($valueFormatted, $subject->valueFormatted);
        $this->assertEquals($timestamp, $subject->timestamp);
    }
}
