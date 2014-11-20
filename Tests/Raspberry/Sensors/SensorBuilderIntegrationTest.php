<?php

namespace Raspberry\Tests\Sensors;

use Exception;
use Raspberry\Sensors\SensorBuilder;
use Raspberry\Sensors\Sensors\SensorInterface;
use Symfony\Component\Console\Output\NullOutput;

class SensorBuilderIntegrationTest extends \PHPUnit_Framework_TestCase {

	public function testSensorType() {
		global $dic;

		/** @var SensorBuilder $sensor_builder */
		$sensor_builder = $dic->get('SensorBuilder');

		$sensor_types = [];
		foreach ($sensor_builder->getSensors() as $sensor) {
			$sensor_type = $sensor->getSensorType();
			$this->assertNotEmpty($sensor_type);
			$this->assertInternalType('string', $sensor_type);

			if (isset($sensor_types[$sensor_type])) {
				throw new Exception(sprintf('Sensor type %s is duplicated'));
			}

			$sensor_types[$sensor_type] = true;
		}
	}

	/**
	 * @dataProvider providerSensors
	 * @param SensorInterface $sensor
	 */
	public function testGetValue(SensorInterface $sensor) {
		$output = new NullOutput();

		$is_supported = $sensor->isSupported($output);
		$this->assertInternalType('boolean', $is_supported);

		if ($is_supported) {
			$value = $sensor->getValue(0);
			$this->assertTrue(is_numeric($value));
		}
	}

	/**
	 * @dataProvider providerSensors
	 * @param SensorInterface $sensor
	 */
	public function testFormatValue(SensorInterface $sensor) {
		$this->assertInternalType('string', $sensor->formatValue(1.1));
		$this->assertInternalType('string', $sensor->getEspeakText(1.1));
	}

	/**
	 * @return array[]
	 */
	public function providerSensors() {
		global $dic;
		$sensor_builder = $dic->get('SensorBuilder');

		return array_map(function(SensorInterface $sensor) {
			return [$sensor];
		}, $sensor_builder->getSensors());
	}

}
