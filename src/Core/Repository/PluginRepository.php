<?php 
namespace App\Core\Repository;

use Doctrine\Persistence\ManagerRegistry;
use App\Core\Entity\Plugin;
use Symfony\Component\Yaml\Yaml;

class PluginRepository extends AbstractRepository
{

    /**
     * Apply config and routes to plugin mechanic
     */
    public function applyConfigAndRoutes()
    {
        $projectDir = $this->parameterBag->get('kernel.project_dir');
        $services = [
            'parameters' => [
                'app.plugins' => []
            ],
            'services' => [
                '_defaults' => [
                    'autowire' => true,
                    'autoconfigure' => true,
                ]
            ],
            'doctrine' => [
                'orm' => [
                    'mappings' => [

                    ]
                ]
            ]
        ];
        $routes = [];
        $plugins = $this->findBy(['status' => Plugin::STATUS_ENABLE], ['priority' => 'ASC']);
        if ($plugins) {
            /** @var Plugin $plugin */
            foreach ($plugins as $plugin) {
                $services['parameters']['app.plugins'][] = $plugin->getCode();
                $services['services']['App\\'.$plugin->getCode().'\\'] = [
                    'resource' => '../../src/'.$plugin->getCode().'/*',
                    'exclude' => '../../src/'.$plugin->getCode().'/{Resource,Deputation,DependencyInjection,Entity,Migrations,Tests}'
                ];
                if (is_dir($projectDir.'/src/'.$plugin->getCode().'/Entity')) {
                    $services['doctrine']['orm']['mappings']['App\\'.$plugin->getCode()] = [
                        'is_bundle' => false,
                        'type' => 'annotation',
                        'dir' => '%kernel.project_dir%/src/'.$plugin->getCode().'/Entity',
                        'prefix' => 'App\\'.$plugin->getCode().'\Entity'
                    ];
                }
                if (is_dir($projectDir.'/src/'.$plugin->getCode().'/Controller')) {
                    $routes['App\\'.$plugin->getCode()] = [
                        'resource' => '../../src/'.$plugin->getCode().'/Controller',
                        'type' => 'annotation'
                    ];
                }
                $config = [];
                $config['imports'][] = ['resource' => '../../src/'.$plugin->getCode().'/{Resource}/{config}/*.yaml'];
                $this->filesystem->dumpFile($projectDir.'/config/services/'.$plugin->getPriority().$plugin->getCode().'.yaml', (new Yaml())->dump($config, 4, 4));
            }
        } else {

        }
        $this->filesystem->dumpFile($projectDir.'/config/services/0plugins.yaml', (new Yaml())->dump($services, 4, 4));
        $this->filesystem->dumpFile($projectDir.'/config/routes/0plugins.yaml', (new Yaml())->dump($routes, 4, 4));
    }
}
