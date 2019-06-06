<?php
namespace App\skeleton\di;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PluginPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $plugins = $container->getParameter('skeleton.activated_plugins');
        foreach ($plugins as $pluginDir) {
            $namespace = substr(strrchr($pluginDir, DIRECTORY_SEPARATOR), 1);
            $path = $pluginDir . DIRECTORY_SEPARATOR . 'templates';
            if (is_dir($path)) {
                $container->getDefinition('twig.loader.native_filesystem')->addMethodCall('addPath', [$path, $namespace]);
            }
        }
    }
}