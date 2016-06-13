<?php

namespace Homie\Expression\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use BrainExe\Core\Annotations\CompilerPass as CompilerPassAnnotation;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @CompilerPassAnnotation
 */
class RegisterProvider implements CompilerPassInterface
{
    const TAG = 'expression_language';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $dispatcher = $container->getDefinition('EventDispatcher');
        $dispatcher->addMethodCall('addCatchall', [new Reference('Expression.Listener')]);

        $language = $container->getDefinition('Expression.Language');
        $language->setArguments([new Reference('service_container')]);

        $serviceIds = $container->findTaggedServiceIds(self::TAG);
        foreach (array_keys($serviceIds) as $serviceId) {
            /** @var ExpressionFunctionProviderInterface $provider */
            $provider = $container->get($serviceId);
            foreach ($provider->getFunctions() as $function) {
                $language->addMethodCall('lazyRegister', [$function->getName(), $serviceId]);
            }
        }
    }
}
