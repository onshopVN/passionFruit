<?php

namespace App\Core;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
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

            // TODO: need better code for human readable and performance
            if (isset($_SERVER['DATABASE_URL'])) {
                $dir = $this->projectDir . '/app';
                $url = $_SERVER['DATABASE_URL'];
                $rdbs = substr($url, 0, strpos($url, '://'));
                $url = substr($url, strpos($url, '://') + 3);
                $userpass = substr($url, 0, strpos($url, '@'));
                $url = substr($url, strpos($url, '@') + 1);
                $hostport = substr($url, 0, strpos($url, '/'));
                $url = substr($url, strpos($url, '/') + 1);
                $nameversion = substr($url, 0);
                $userpass = explode(':', $userpass);
                $hostport = explode(':', $hostport);
                $nameversion = explode('?', $nameversion);     
                try {
                    $pdo = new \PDO($rdbs . ':host=' . $hostport[0] . (isset($hostport[1]) ? ';port=' . $hostport[1] : '') . ';dbname=' . $nameversion[0], $userpass[0], isset($userpass[1]) ? $userpass[1]: null);
                    foreach($pdo->query('SELECT * from core_plugin ORDER BY priority ASC') as $row) {
                        $this->plugins[$row['code']] = [
                            'code' => $row['code'],
                            'priority' => $row['priority'],
                            'path' => $dir . '/' . $row['code']
                        ];
                    }
                    $pdo = null;
                } catch (\PDOException $e) {
                    // TODO: monitor + log
                }
            }
        }
        
        return $this->plugins;
    }
}