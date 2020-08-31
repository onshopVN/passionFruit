<?php 
namespace App\Core\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Core\Event\EntityAfterUpdateEvent;
use App\Core\Entity\Plugin;
use App\Core\Repository\PluginRepository;

class PluginES implements EventSubscriberInterface
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
        if ($event->hasUpdate('status')) {
            /** @var Plugin $plugin */
            $plugin = $event->getEntity();
            if ($plugin->isEnable()) {
                $this->pluginR->assertRequired($plugin);
                $this->pluginR->applyWorkflow();
            }
            if ($plugin->isDisable()) {
                $this->pluginR->applyWorkflow();
            }
        }
    }
}
