<?php 
namespace App\Core\Event;

class EntityAfterUpdateEvent extends EntityEvent
{
    /**
     * Hold update values
     * @var array 
     */
    protected $updates = [];

    /**
     * Get $updates
     * 
     * @return array 
     */
    public function getUpdates() : array 
    {
        return $this->updates;
    }

    /**
     * Set $updates
     * @param array $updates
     * @return $this 
     */
    public function setUpdates(array $updates) : self
    {
        $this->updates = $updates;
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
     * Check value has updated or not
     * @param string name 
     * @return bool 
     */
    public function hasUpdate(string $name) : bool
    {
        return array_key_exists($name, $this->getUpdates());
    }

    /**
     * Get update value
     * @return mixed
     */
    public function getUpdate(string $name)
    {
        return $this->updates[$name];
    }
}