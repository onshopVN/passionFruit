<?php

namespace App\skeleton;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;
    const CONFIG_EXTS = '.{php,xml,yaml,yml}';
    protected $pluginDir;
    protected $projectDir;

    public function registerBundles()
    {
        $contents = require $this->getProjectDir() . '/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }
        // for plugins
        $plugins = $this->getPluginsActived();
        foreach ($plugins as $pluginDir) {
            $bundles = $pluginDir . '/config/bundles.php';
            if (!file_exists($bundles)) continue;
            $contents = require $bundles;
            foreach ($contents as $class => $envs) {
                if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                    yield new $class();
                }
            }
        }
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {
        $container->addResource(new FileResource($this->getProjectDir() . '/config/bundles.php'));
        $container->setParameter('container.dumper.inline_class_loader', true);
        $confDir = $this->getProjectDir() . '/config';
        $loader->load($confDir . '/{packages}/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{packages}/' . $this->environment . '/**/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{services}' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{services}_' . $this->environment . self::CONFIG_EXTS, 'glob');
        // for plugins
        $plugins = $this->getPluginsActived('krsort'); //dump($plugins);die;
        foreach ($plugins as $pluginDir) {
            $pluginConfigDir = $pluginDir . '/config';
            $bundles = $pluginConfigDir . '/bundles.php';
            if (file_exists($bundles))
                $container->addResource(new FileResource($bundles));
            $loader->load($pluginConfigDir . '/{Packages}/*' . self::CONFIG_EXTS, 'glob');
            $loader->load($pluginConfigDir . '/{Packages}/' . $this->environment . '/**/*' . self::CONFIG_EXTS, 'glob');
            $loader->load($pluginConfigDir . '/{Services}' . self::CONFIG_EXTS, 'glob');
            $loader->load($pluginConfigDir . '/{Services}_' . $this->environment . self::CONFIG_EXTS, 'glob');
        }
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $confDir = $this->getProjectDir() . '/config';
        $routes->import($confDir . '/{routes}/*' . self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir . '/{routes}/' . $this->environment . '/**/*' . self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir . '/{routes}' . self::CONFIG_EXTS, '/', 'glob');
        // for plugins
        $plugins = $this->getPluginsActived();
        foreach ($plugins as $pluginDir) {
            $pluginConfig = $pluginDir . '/config';
            if (file_exists($pluginConfig)) {
                $routes->import($pluginConfig . '/{Route}/*' . self::CONFIG_EXTS, '/', 'glob');
                $routes->import($pluginConfig . '/{Route}/' . $this->environment . '/**/*' . self::CONFIG_EXTS, '/', 'glob');
                $routes->import($pluginConfig . '/{Route}' . self::CONFIG_EXTS, '/', 'glob');
            }
            $pluginController = $pluginDir . '/controller';
            if (file_exists($pluginController)) {
                $routes->import($pluginController, '/', 'annotation');
            }

        }
    }

    /**
     * @return string The project root dir
     * Redefine because using composer.json in plugin
     */
    public function getProjectDir()
    {
        if (null === $this->projectDir) {
            $r = new \ReflectionObject($this);
            $dir = $rootDir = \dirname($r->getFileName());//Core
            $dir = \dirname($dir); //src
            $dir = \dirname($dir);
            $this->projectDir = $dir;
        }
        return $this->projectDir;
    }

    public function getPluginDir()
    {
        $this->pluginDir = $this->getProjectDir() . '/src';
        return $this->pluginDir;
    }

    // TODO: make cached for them
    public function getPluginsActived(string $sort = 'ksort')
    {
        $pluginsDir = $this->getPluginDir();
        $plugins = array_filter(glob($pluginsDir . '/*'), 'is_dir');
        $pluginsFilter = [];
        foreach ($plugins as $plugin) {
            $composerFile = $plugin . '/composer.json';
            if (file_exists($composerFile)) {
                $petsJson = json_decode(file_get_contents($composerFile));
                if ($petsJson->extra->status == 'actived') {
                    $pluginsFilter[$petsJson->extra->priority] = $plugin;
                }
            }
        }
        $sort($pluginsFilter);
        return $pluginsFilter;
    }
}