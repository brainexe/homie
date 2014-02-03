<?php

namespace Raspberry\DIC;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ConsoleCompilerPass implements CompilerPassInterface {

	public function process(ContainerBuilder $container) {
		$definition = $container->getDefinition('Console');

		$taggedServices = $container->findTaggedServiceIds('console');
		foreach ($taggedServices as $id => $attributes) {
			$definition->addMethodCall('add', [new Reference($id)]);
		}
	}
}