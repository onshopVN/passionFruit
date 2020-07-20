<?php
namespace App\Core\Event;

class EntityEvent extends \Symfony\Contracts\EventDispatcher\Event
{
    /**
     * @var mix 
     */
    protected $entity;

    /**
     * @param $entity
     */
    public function __construct($entity = null)
    {
        $this->setEntity($entity);
    }

    /**
     * Get $entity
     * @return mix 
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set $entity
     * @return $this 
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * Get name
     * @return string 
     */
    public function getName() : string 
    {
        if (!$this->getEntity()) {
            throw new \LogicException('An entity need be set before getName() execute');
        }

        $className = get_class($this->getEntity());
        preg_match('/App\\\\([a-zA-Z]+)\\\\Entity\\\\([a-zA-Z]+)/', $className, $matches);
        if (count($matches) < 3) {
            throw new \LogicException('Entity was valid name');
        }

        $plugin = preg_replace('/(?<=\\w)(?=[A-Z])/',"_$1", $matches[1]); 
        $entity = preg_replace('/(?<=\\w)(?=[A-Z])/',"_$1", $matches[2]); 

        return strtolower($plugin) . '_' . strtolower($entity); 
    }
}