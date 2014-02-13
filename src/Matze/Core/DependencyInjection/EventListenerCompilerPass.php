<?php

namespace Matze\Core\DependencyInjection;

use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations as DI;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @DI\Service(tags={{"name" = "compiler_pass"}})
 */
class EventListenerCompilerPass implements CompilerPassInterface {

	public function process(ContainerBuilder $container) {
		$services = $container->findTaggedServiceIds('event_subscriber');

		$event_dispatcher = $container->getDefinition('EventDispatcher');

		foreach (array_keys($services) as $service_id) {
			$event_dispatcher->addMethodCall('addSubscriber', [new Reference($service_id)]);
		}
	}
}