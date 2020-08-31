<?php 
namespace App\Core\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Finder\Finder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\Process\Process;
use App\Core\Entity\Plugin;

class PluginRepository extends AbstractRepository
{
    /**
     * @var ParameterBagInterface
     */
    protected $parameterBag;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ParameterBagInterface $parameterBag
     * @param ManagerRegistry $managerRegistry
     * @param string entityClass
     */
    public function __construct(
        ContainerInterface $container,
        ParameterBagInterface $parameterBag,
        ManagerRegistry $managerRegistry, 
        string $entityClass = ''
    ) {
        parent::__construct($managerRegistry, $entityClass);
        $this->container = $container;
        $this->parameterBag = $parameterBag;
    }

    /**
     * Check required
     * 
     * @return bool
     * @throws \LogicException
     */
    public function assertRequired(Plugin $plugin)
    {
        $required = $plugin->getRequired('serialize');
        if ($required && is_array($required)) {
            foreach ($required as $r) {
                $process = new Process(['php', 'bin/console', 'debug:container', $r]);
                $process->run();
                if (!$process->isSuccessful()) {
                    throw new \LogicException($r . 'need implemented before enable this plugin.');
                }
            }
        }
        return true;
    }

    /**
     * Apply plugin mechanic
     */
    public function applyWorkflow()
    {
        $prjDir = $this->parameterBag->get('kernel.project_dir');

        // disable 
        $plugins = $this->findBy(['status' => Plugin::STATUS_DISABLE], ['priority' => 'ASC']);
        foreach ($plugins as $plugin) {
            $srcDir = $prjDir.'/src/'.$plugin->getCode();
            $dstDir = $prjDir.'/app/'.$plugin->getCode();
            if ($this->filesystem->exists($dstDir)) {
                $this->filesystem->remove($dstDir);
            }

            // skeleton
            $sktDir = $srcDir.'/Skeleton';
            if ($this->filesystem->exists($sktDir)) {
                $finder = Finder::create()->files()->in($sktDir);
                if ($finder->hasResults()) {
                    foreach ($finder as $f) {
                        $targetFile = str_replace('src/'.$plugin->getCode(), 'app', $f->getRealPath());
                        if ($this->filesystem->exists($targetFile)) {
                            $this->filesystem->remove($targetFile);
                        }
                    }
                }
            }
        }

        // enable
        $plugins = $this->findBy(['status' => Plugin::STATUS_ENABLE], ['priority' => 'ASC']);
        foreach ($plugins as $plugin) {
            $srcDir = $prjDir.'/src/'.$plugin->getCode();
            $dstDir = $prjDir.'/app/'.$plugin->getCode();
            if (!$this->filesystem->exists($dstDir)) {
                $this->filesystem->symlink($srcDir, $dstDir);

                // skeleton
                $sktDir = $srcDir.'/Skeleton';
                if ($this->filesystem->exists($sktDir)) {
                    $finder = Finder::create()->files()->in($sktDir);
                    if ($finder->hasResults()) {
                        foreach ($finder as $f) {
                            $this->filesystem->symlink($f->getRealPath(), str_replace('src/'.$plugin->getCode(), 'app', $f->getRealPath()));
                        }
                    }
                }
            }
        }
    }
}
