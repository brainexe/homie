<?php

namespace Tests\Raspberry\Sensors;

use Exception;
use Raspberry\Sensors\SensorBuilder;
use Raspberry\Sensors\Sensors\SensorInterface;
use Symfony\Component\Console\Output\NullOutput;

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
     * @param SensorInterface $sensor
     */
    public function testGetValue(SensorInterface $sensor)
    {
        $output = new NullOutput();

        $isSupported = $sensor->isSupported($output);
        $this->assertInternalType('boolean', $isSupported);

        if ($isSupported) {
            $value = $sensor->getValue(0);
            $this->assertTrue(is_numeric($value));
        }
    }

    /**
     * @dataProvider providerSensors
     * @param SensorInterface $sensor
     */
    public function testFormatValue(SensorInterface $sensor)
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

        return array_map(function(SensorInterface $sensor) {
            return [$sensor];
        }, $builder->getSensors());
    }
}
