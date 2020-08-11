<?php 
namespace App\Core\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Finder\Finder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Core\Entity\Plugin;

class PluginRepository extends AbstractRepository
{
    /**
     * @var ParameterBagInterface
     */
    protected $parameterBag;

    /**
     * @param ParameterBagInterface $parameterBag
     * @param ManagerRegistry $managerRegistry
     * @param string entityClass
     */
    public function __construct(
        ParameterBagInterface $parameterBag,
        ManagerRegistry $managerRegistry, 
        string $entityClass = ''
    ) {
        parent::__construct($managerRegistry, $entityClass);
        $this->parameterBag = $parameterBag;
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
