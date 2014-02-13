<?php

namespace Matze\Core\DependencyInjection;

use Raspberry\DIC\SensorCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class GlobalCompilerPass implements CompilerPassInterface {

	public function process(ContainerBuilder $container) {
		$services = $container->findTaggedServiceIds('compiler_pass');

		foreach (array_keys($services) as $service_ids) {
			$container->get($service_ids)->process($container);
		}
	}
}