<?php

namespace Homie\Filesystem;

use BrainExe\Core\Annotations\CompilerPass as CompilerPassAnnotation;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @CompilerPassAnnotation("Filesystem.CompilerPass")
 */
class CompilerPass implements CompilerPassInterface
{

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $host = $container->getParameter('filesystem.remote.url');
        $user = $container->getParameter('filesystem.remote.username');

        if ($host && $user) {
            if ($container->getParameter('filesystem.remote.cache')) {
                $container->setAlias('RemoteFilesystem', 'CachedRemoteFilesystem');
            } else {
                $container->setAlias('RemoteFilesystem', 'WebdavRemoteFilesystem');
            }
        }
    }
}
