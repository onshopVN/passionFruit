<?php 
namespace App\Core\Event;

class EntityAfterCreateEvent extends EntityEvent 
{
    /**
     * @inheritdoc
     * @return string 
     */
    public function getName(): string
    {
        return parent::getName() . '_after_create';
    }
}