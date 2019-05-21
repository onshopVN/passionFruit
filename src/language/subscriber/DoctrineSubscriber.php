<?php
namespace App\language\subscriber;

use App\authen\entity\Authen;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\EventArgs;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class DoctrineSubscriber implements EventSubscriber
{
    private $session;

    /**
     * DoctrineSubscriber constructor.
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function loadClassMetadata(EventArgs $eventArgs)
    {
//        $ea = $this->getEventAdapter($eventArgs);
//        $this->loadMetadataForObjectClass($ea->getObjectManager(), $eventArgs->getClassMetadata());
    }

//    public function onKernelRequest(GetResponseEvent $event)
//    {
//        $request = $event->getRequest();
//        if (!$request->hasPreviousSession()) {
//            return;
//        }
//        // if no explicit locale has been set on this request, use one from the session
//        $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
//    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $om = $args->getObjectManager();
        $object = $args->getObject();
        $meta = $om->getClassMetadata(get_class($object));

        $test = $meta->getReflectionClass();
        if($test->hasProperty('email'))
        {
            $test1 = $test->getProperty('email');
            $reader = new AnnotationReader();
//            $doc = $reader->getPropertyAnnotation(
//                $test1,
//                Authen::class
//            );
            $doc = $reader->getPropertyAnnotations($test1);

            if( isset($doc[0]->options['lang']))
            {
                $test1->setAccessible(true);
                $test1->setValue($object, $object->getLanguage('email',$this->session->get('_locale')));
            }
        }

    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $om = $args->getObjectManager();
        $object = $args->getObject();
        $meta = $om->getClassMetadata(get_class($object));

        $test = $meta->getReflectionClass();
        if($test->hasProperty('email'))
        {
            $test1 = $test->getProperty('email');
            $reader = new AnnotationReader();
//            $doc = $reader->getPropertyAnnotation(
//                $test1,
//                Authen::class
//            );
            $doc = $reader->getPropertyAnnotations($test1);

            if( isset($doc[0]->options['lang']))
            {
                $test2 = $test->getProperty('email_'.$this->session->get('_locale'));
                $test1->setAccessible(true);
                $test2->setAccessible(true);
                $object->setLanguage('email',$this->session->get('_locale'), $test1->getValue($object));
//                dump($test1->getValue($object));
//                dump($test2);die;

            }
        }

    }


    public function preUpdate(LifecycleEventArgs $args)
    {
        $om = $args->getObjectManager();
        $object = $args->getObject();
        $meta = $om->getClassMetadata(get_class($object));

        $test = $meta->getReflectionClass();
        if($test->hasProperty('email'))
        {
            $test1 = $test->getProperty('email');
            $reader = new AnnotationReader();
//            $doc = $reader->getPropertyAnnotation(
//                $test1,
//                Authen::class
//            );
            $doc = $reader->getPropertyAnnotations($test1);

            if( isset($doc[0]->options['lang']))
            {
                $test2 = $test->getProperty('email_'.$this->session->get('_locale'));
                $test1->setAccessible(true);
                $test2->setAccessible(true);
                $object->setLanguage('email',$this->session->get('_locale'), $test1->getValue($object));
//                dump($test1->getValue($object));
//                dump($test2);die;

            }
        }

    }


    public  function getSubscribedEvents()
    {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            Events::postLoad,
            Events::prePersist,
//            Events::postPersist,
            Events::preUpdate,
//            'onFlush',
            Events::loadClassMetadata
        ];
    }


    // get field translatable
    public function getTranslatableLocale($object, $meta, $om = null)
    {
        $locale = $this->locale;
//        if (isset(self::$configurations[$this->name][$meta->name]['locale'])) {
            /** @var \ReflectionClass $class */
            $class = $meta->getReflectionClass();
        dump($class->getMethods()); die;
            $reflectionProperty = $class->getProperty('email');
            $reflectionProperty->setAccessible(true);
            $value = $reflectionProperty->getValue($object);
        dump($value); die;
            if (is_object($value) && method_exists($value, '__toString')) {
                $value = (string) $value;
            }
            if ($this->isValidLocale($value)) {
                $locale = $value;
            }
//        }
        return $locale;
    }

}