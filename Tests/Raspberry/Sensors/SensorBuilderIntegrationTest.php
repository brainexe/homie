<?php

namespace Tests\Raspberry\Sensors;

use Exception;
use Raspberry\Sensors\Interfaces\Sensor;
use Raspberry\Sensors\SensorBuilder;

class SensorBuilderIntegrationTest extends \PHPUnit_Framework_TestCase
{

    public function testSensorType()
    {
        global $dic;

        /** @var SensorBuilder $builder */
        $builder = $dic->get('SensorBuilder');

        $sensorTypes = [];
        foreach ($builder->getSensors() as $sensor) {
            $sensorType = $sensor->getSensorType();
            $this->assertNotEmpty($sensorType);
            $this->assertInternalType('string', $sensorType);

            if (isset($sensorTypes[$sensorType])) {
                throw new Exception(sprintf('Sensor type %s is duplicated'));
            }

            $sensorTypes[$sensorType] = true;
        }
    }

    /**
     * @dataProvider providerSensors
     * @param Sensor $sensor
     */
    public function testFormatValue(Sensor $sensor)
    {
        $this->assertInternalType('string', $sensor->formatValue(1.1));
        $this->assertInternalType('string', $sensor->getEspeakText(1.1));
    }

    /**
     * @return array[]
     */
    public function providerSensors()
    {
        global $dic;
        $builder = $dic->get('SensorBuilder');

        return array_map(function(Sensor $sensor) {
            return [$sensor];
        }, $builder->getSensors());
    }
}
