<?php

namespace Raspberry\DIC;

use Raspberry\Dashboard\WidgetInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @CompilerPass
 */
class WidgetCompilerPass implements CompilerPassInterface {

	const TAG = 'widget';

	public function process(ContainerBuilder $container) {
		/** @var Definition $definition */
		$definition = $container->getDefinition('WidgetFactory');

		$taggedServices = $container->findTaggedServiceIds(self::TAG);
		foreach ($taggedServices as $id => $attributes) {
			/** @var WidgetInterface $service */
			$service = $container->get($id);

			$definition->addMethodCall('addWidget', [$service->getId(), new Reference($id)]);
		}
	}

}
