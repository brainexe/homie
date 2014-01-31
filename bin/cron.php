<?php

use Raspberry\Sensors\SensorBuilder;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\SensorValuesGateway;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/** @var ContainerBuilder $dic */
$dic = include __DIR__ . '/../src/bootstrap.php';

$minute = date('i');

/** @var SensorGateway $sensor_gateway */
$sensor_gateway = $dic->get('SensorGateway');
/** @var SensorValuesGateway $sensor_values_gateway */
$sensor_values_gateway = $dic->get('SensorValuesGateway');
/** @var SensorBuilder $sensor_builder */
$sensor_builder = $dic->get('SensorBuilder');
$sensors = $sensor_gateway->getSensors();

foreach ($sensors as $sensor_data) {
	$interval = $sensor_data['interval'];
	if ($minute % $interval === 0) {
		$sensor = $sensor_builder->build($sensor_data);

		$value = $sensor->getValue($sensor_data['pin']);

		if ($value === null) {
			continue;
		}

		$sensor_values_gateway->addValue($sensor_data['id'], $value);

		sleep(2);
	}

	if ($minute == 0 && date('G') == 0) {
		$sensor_values_gateway->deleteOldValues(1, 25);
		$sensor_values_gateway->deleteOldValues(3, 50);
		$sensor_values_gateway->deleteOldValues(5, 75);
		$sensor_values_gateway->deleteOldValues(10, 90);
	}
}
