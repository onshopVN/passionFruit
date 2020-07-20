<?php 
namespace App\Core\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Core\Event\EntityAfterUpdateEvent;
use App\Core\Entity\Plugin;
use App\Core\Repository\PluginRepository;

class PluginSubscriber implements EventSubscriberInterface
{
    /**
     * @var PluginRepository
     */
    protected $pluginR;

    /**
     * @param PluginRepository $pluginRepository
     */
    public function __construct(
        PluginRepository $pluginRepository
    ) {
        $this->pluginR = $pluginRepository;  
    }

    /**
     * @return array 
     */
    public static function getSubscribedEvents()
    {
        return [
            'core_plugin_after_update' => ['onPluginUpdate']
        ];
    }

    /**
     * @param EntityAfterUpdateEvent $event
     */
    public function onPluginUpdate(EntityAfterUpdateEvent $event)
    {
        if ($event->hasChangeField('status')) {
            /** @var Plugin $plugin */
            $plugin = $event->getEntity();
            if ($plugin->isEnable()) {
                // dump config 
                // route
                $this->pluginR->applyConfigAndRoutes();
            }
            if ($plugin->isDisable()) {
                // dump config
                // route 
                $this->pluginR->applyConfigAndRoutes();
            }
        }
    }
}
