<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

$builder = new ContainerBuilder();

$builder->setParameter('db.host', 'localhost');
$builder->setParameter('db.database', 'raspberry');
$builder->setParameter('db.user', 'root');
$builder->setParameter('db.password', '');

$builder->register('PDO', 'PDO')
	->addArgument("mysql:host=%db.host%;dbname=%db.database%")
	->addArgument("%db.user%")
	->addArgument("%db.password%");

$builder->register('SensorBuilder', 'Raspberry\Sensors\SensorBuilder');
$builder->register('SensorGateway', 'Raspberry\Sensors\SensorGateway')->addMethodCall('setPDO', [new Reference('PDO')]);
$builder->register('SensorValuesGateway', 'Raspberry\Sensors\SensorValuesGateway')->addMethodCall('setPDO', [new Reference('PDO')]);

return $builder;
