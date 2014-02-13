<?php

namespace Raspberry\DIC;

use Raspberry\Controller\ControllerInterface;
use Raspberry\Sensors\Sensors\SensorInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SensorCompilerPass implements CompilerPassInterface {

	public function process(ContainerBuilder $container) {
		$definition = $container->getDefinition('SensorBuilder');

		$taggedServices = $container->findTaggedServiceIds('sensor');
		foreach ($taggedServices as $id => $attributes) {
			/** @var SensorInterface $service */
			$service = $container->get($id);

			$definition->addMethodCall('addSensor', [$service->getSensorType(), new Reference($id)]);
		}
	}

}