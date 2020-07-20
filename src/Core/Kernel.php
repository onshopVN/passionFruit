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

        $container->import($this->getProjectDir().'/config/{plugins}/*/*s.yaml');
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

        $routes->import($this->getProjectDir().'/config/{plugins}/*/routes/*.yaml');
    }
}