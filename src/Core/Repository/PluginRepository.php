<?php 
namespace App\Core\Repository;

use Doctrine\Persistence\ManagerRegistry;
use App\Core\Entity\Plugin;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;

class PluginRepository extends AbstractRepository
{
    protected $kernel;

    public function __construct(
        KernelInterface $kernel,
        ManagerRegistry $managerRegistry, 
        string $entityClass = ''
    ) {
        parent::__construct($managerRegistry, $entityClass);
        $this->kernel = $kernel;
    }

    /**
     * Apply plugin mechanic
     */
    public function applyWorkflow()
    {
        if (method_exists($this->kernel, 'getPlugins')) {
            $currentPlugins = $this->kernel->getPlugins();
            foreach ($currentPlugins as $plugin) {
                $this->filesystem->remove($plugin['path']);
            }
        }

        $plugins = $this->findBy(['status' => Plugin::STATUS_ENABLE]);
        foreach ($plugins as $plugin) {
            $this->filesystem->symlink($this->kernel->getProjectDir().'/src/'.$plugin->getCode(), $this->kernel->getProjectDir().'/app/'.$plugin->getCode());
        }
    }
}
