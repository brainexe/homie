<?php

namespace Homie\Expression\CompilerPass;

use Homie\Expression\Language;
use Symfony\Component\DependencyInjection\Argument\ClosureProxyArgument;
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
        $dispatcher = $container->findDefinition('EventDispatcher');
        $dispatcher->addMethodCall('addCatchall', [new Reference('Expression.Listener')]);

        $language = $container->findDefinition(Language::class);
        $language->setArguments([new Reference('service_container')]);

        $serviceIds = $container->findTaggedServiceIds(self::TAG);
        foreach (array_keys($serviceIds) as $serviceId) {
            /** @var ExpressionFunctionProviderInterface $provider */
            $class = $container->getDefinition($serviceId)->getClass();
            $providerReflection = new \ReflectionClass($class);
            $provider = $providerReflection->newInstanceWithoutConstructor();
            foreach ($provider->getFunctions() as $function) {
                $language->addMethodCall('lazyRegister', [
                    $function->getName(),
                    new ClosureProxyArgument($serviceId, 'getFunctions')
                ]);
            }
        }
    }
}
