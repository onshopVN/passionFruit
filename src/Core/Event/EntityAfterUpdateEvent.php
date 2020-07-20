<?php 
namespace App\Core\Event;

class EntityAfterUpdateEvent extends EntityEvent
{
    protected $oldEntity;

    /**
     * Get $oldEntity
     * @return mix
     */
    public function getOldEntity()
    {
        return $this->oldEntity;
    }

    /**
     * Set $oldEntity
     * @param $oldEntity
     * @return $this 
     */
    public function setOldEntity($oldEntity)
    {
        $this->oldEntity = $oldEntity;
        return $this;
    }

    /**
     * Set $entity
     * @return $this 
     */
    public function setEntity($entity)
    {
        parent::setEntity($entity);

        if (!$this->getOldEntity()) {
            $oldEntity = clone $this->getEntity();
            $this->setOldEntity($oldEntity);
        }
        return $this;
    }

    /**
     * @inheritdoc
     * @return string 
     */
    public function getName(): string
    {
        return parent::getName() . '_after_update';
    }

    /**
     * Check value different between oldstate and current entity 
     * @param string name 
     * @return bool 
     */
    public function hasChangeField(string $name) : bool
    {
        $method = 'get' . ucfirst($name);
        if (!method_exists($this->getEntity(), $method) || !method_exists($this->getOldEntity(), $method)) {
            return false;
        }

        return $this->getEntity()->$method() != $this->getOldEntity()->$method();
    }
}