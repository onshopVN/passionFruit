<?php 
namespace App\Core\Subscriber;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use App\Core\Repository\AbstractRepository;

class DoctrineSubscriber implements EventSubscriber
{
    /**
     * {@inheritDoc}
     *
     * @return string[]
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postLoad
        ];
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function postLoad(LifecycleEventArgs $event)
    {
        try {
            $entity = $event->getEntity();
            $repository = $event->getObjectManager()->getRepository(get_class($entity));
            if ($repository instanceof AbstractRepository) {
                $repository->snapshot($entity);
            }
        } catch (\Exception $e) {
            // silence
        }
    }
}
