<?php

namespace App\Core;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

class Kernel extends \Symfony\Component\HttpKernel\Kernel
{
    use MicroKernelTrait;

    /**
     * @var string 
     */
    protected $projectDir;

    /**
     * @var array 
     */
    protected $plugins;

    /**
     * @inheritdoc
     * @return string 
     */
    public function getProjectDir() : string 
    {
        if (null === $this->projectDir) {
            $r = new \ReflectionObject($this);

            if (!is_file($dir = $r->getFileName())) {
                throw new \LogicException(sprintf('Cannot auto-detect project dir for kernel of class "%s".', $r->name));
            }

            $dir = $rootDir = \dirname($dir);
            while (!is_file($dir.'/composer.lock')) {
                if ($dir === \dirname($dir)) {
                    return $this->projectDir = $rootDir;
                }
                $dir = \dirname($dir);
            }
            $this->projectDir = $dir;
        }

        return $this->projectDir;
    }

    /**
     * Configure container
     */
    protected function configureContainer(ContainerConfigurator $container, LoaderInterface $loader)
    {
        $container->import($this->getProjectDir().'/config/{packages}/*.yaml');
        $container->import($this->getProjectDir().'/config/{packages}/'.$this->environment.'/*.yaml');

        if (is_file($this->getProjectDir().'/config/services.yaml')) {
            $container->import($this->getProjectDir().'/config/{services}.yaml');
            $container->import($this->getProjectDir().'/config/{services}_'.$this->environment.'.yaml');
        } elseif (is_file($path = $this->getProjectDir().'/config/services.php')) {
            (require $path)($container->withPath($path), $this);
        }

        // for plugins
        $plugins = $this->getPlugins();
        uasort($plugins, fn($p1,$p2) => $p1['priority'] > $p2['priority']); // sort by priority
        foreach ($plugins as $plugin) {
            // config
            $config = $plugin['path'].'/Resource/config';
            if (is_dir($config)) {
                $container->import($config.'/{packages}/*.yaml');
                $container->import($config.'/{packages}/'.$this->environment.'/*.yaml');
                $container->import($config.'/{services}.yaml');
                $container->import($config.'/{services}_'.$this->environment.'.yaml');
            }
            // twig
            $twig = $plugin['path'].'/Resource/templates';
            if (is_dir($twig)) {
                $container->extension('twig', [
                    'paths' => [
                        $twig => null
                    ]
                ]);
            }
            // translation
            $translation = $plugin['path'].'/Resource/translations';
            if (is_dir($translation)) {
                $container->extension('framework', [
                    'translator' => [
                        'path' => $translation
                    ]
                ]);
            }
            // entity 
            $entity = $plugin['path'].'/Entity';
            if (is_dir($entity)) {
                $container->extension('doctrine', [
                    'orm' => [
                        'mappings' => [
                            'App\\'.$plugin['code'] => [
                                'is_bundle' => false,
                                'type' => 'annotation',
                                'dir' => $entity,
                                'prefix' => 'App\\'.$plugin['code'],
                                'alias' => 'App\\'.$plugin['code']
                            ]
                        ]
                    ]
                ]);
            }
        }
    }

    /**
     * @inheritdoc
     */
    protected function configureRoutes(RoutingConfigurator $routes)
    {
        $routes->import($this->getProjectDir().'/config/{routes}/'.$this->environment.'/*.yaml');
        $routes->import($this->getProjectDir().'/config/{routes}/*.yaml');

        if (is_file($this->getProjectDir().'/config/routes.yaml')) {
            $routes->import($this->getProjectDir().'/config/{routes}.yaml');
        } elseif (is_file($path = $this->getProjectDir().'/config/routes.php')) {
            (require $path)($routes->withPath($path), $this);
        }

        $plugins = $this->getPlugins();
        uasort($plugins, fn($p1,$p2) => $p1['priority'] > $p2['priority']); // sort by priority
        foreach ($plugins as $plugin) {
            $dir = $plugin['path'].'/Controller';
            if (is_dir($dir)) {
                $routes->import($dir, 'annotation');
            }
        }
    }

    /**
     * Get plugins
     * 
     * @return array 
     */
    public function getPlugins() : array 
    {
        if (null === $this->plugins) {
            $this->plugins = [];
            $dir = $this->projectDir . '/app';
            $nodes = array_filter(glob($dir . '/*'), 'is_dir');
            foreach ($nodes as $node) {
                $composer = $node.'/composer.json';
                if (is_file($composer)) {
                    $plugin = json_decode(file_get_contents($composer), true);
                    if (isset($plugin['extra'])) {
                        $this->plugins[$plugin['extra']['code']] = [
                            'code' => $plugin['extra']['code'],
                            'priority' => $plugin['extra']['priority'],
                            'path' => $dir.'/'.$plugin['extra']['code']
                        ];
                    }
                }
            }
        }
        
        return $this->plugins;
    }
}