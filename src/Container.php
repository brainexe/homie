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

$builder->register('Chart', 'Raspberry\Chart\Chart');
$builder->register('LocalClient', 'Raspberry\Client\LocalClient');

$builder->register('SensorBuilder', 'Raspberry\Sensors\SensorBuilder');
$builder->register('SensorGateway', 'Raspberry\Sensors\SensorGateway')->addMethodCall('setPDO', [new Reference('PDO')]);
$builder->register('SensorGateway', 'Raspberry\Sensors\SensorGateway')->addMethodCall('setPDO', [new Reference('PDO')]);

$builder->register('RadioGateway', 'Raspberry\Radio\RadioGateway')->addMethodCall('setPDO', [new Reference('PDO')]);
$builder->register('RadioController', 'Raspberry\Radio\RadioController');
$builder->register('Radios', 'Raspberry\Radio\Radios')
	->addMethodCall('setRadioController', [new Reference('RadioController')])
	->addMethodCall('setRadioGateway', [new Reference('RadioGateway')]);

return $builder;
