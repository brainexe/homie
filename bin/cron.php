<?php

use Raspberry\Sensors\SensorBuilder;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\SensorValuesGateway;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/** @var ContainerBuilder $dic */
$dic = include '../src/bootstrap.php';

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
		$sensor_values_gateway->addValue($sensor_data['id'], $value);
	}
}
