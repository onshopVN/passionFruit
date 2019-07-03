<?php

namespace App\language\subscriber;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\EventArgs;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class DoctrineSubscriber implements EventSubscriber
{
    private $session;
    private $language = 'en';
    private $seperateChar = '_';

    /**
     * DoctrineSubscriber constructor.
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        dump('postLoad');
        $om = $args->getObjectManager();
        $object = $args->getObject();
        $classMetadata = $om->getClassMetadata(get_class($object));

        $reflectionClass = $classMetadata->getReflectionClass();
        $this->changeValueTranslatableFields($object, $reflectionClass);
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        dump('prePersist');
        $om = $args->getObjectManager();
        $object = $args->getObject();
        $classMetadata = $om->getClassMetadata(get_class($object));

        $reflectionClass = $classMetadata->getReflectionClass();
        $this->changeValueTranslatableFields($object, $reflectionClass, false);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        dump('preUpdate');
        $om = $args->getObjectManager();
        $object = $args->getObject();
        $classMetadata = $om->getClassMetadata(get_class($object));

        $reflectionClass = $classMetadata->getReflectionClass();
        $this->changeValueTranslatableFields($object, $reflectionClass, false);
    }

    public function getSubscribedEvents()
    {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            Events::postLoad,
            Events::prePersist,
//            Events::postPersist,
            Events::preUpdate,
//            'onFlush',
//            Events::loadClassMetadata
        ];
    }

    public function getTranslatableFields($reflectionClass)
    {
        $properties = $reflectionClass->getProperties();
        $propertiesLanguage = [];
        foreach ($properties as $property) {
            $reader = new AnnotationReader();
            $doc = $reader->getPropertyAnnotations($property);
            if (isset($doc[0]->options['translate'])) {
                $propertiesLanguage[$doc[0]->options['translate']] = $property->name;
            }
        }
        return $propertiesLanguage;
    }

    public function getTranslatableField($reflectionClass, $fieldName)
    {
        $fieldLang = $fieldName . $this->seperateChar . $this->getCurrentLanguage();
        if ($reflectionClass->hasProperty($fieldLang))
            return $fieldLang;
        return false;
    }

    public function changeValueTranslatableFields($object, $reflectionClass, $postLoad = 'true')
    {
        $langProperties = $this->getTranslatableFields($reflectionClass);

        foreach ($langProperties as $key => $value) {
            $keyLang = $this->getTranslatableField($reflectionClass, $key);
            if ($keyLang) {
                $property = $reflectionClass->getProperty($key);
                $property->setAccessible(true);

                if ($postLoad) {
                    $property->setValue($object, $object->getLanguage($key, $this->getCurrentLanguage()));
                } else {
                    $propertyLang = $reflectionClass->getProperty($keyLang);
                    $propertyLang->setAccessible(true);
                    $object->setLanguage($key, $this->getCurrentLanguage(), $property->getValue($object));
                }
            }
        }
    }

    public function getCurrentLanguage()
    {
        $this->language = $this->session->get('_locale') ? $this->session->get('_locale') : 'en';
        return $this->language;
    }
}