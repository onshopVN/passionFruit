<?php 
namespace App\Core\Entity;

abstract class AbstractEntity
{
    /**
     * Store values of property before updates them
     * @var array 
     */
    protected $updates = [];

    /**
     * Track value of field before update new value for it. 
     * 
     * @param string $key 
     * @param mixed $value 
     * @return $this 
     */
    public function trackUpdate(?string $key = null, $value = null) : self 
    {
        if (method_exists($this, 'getId') && $this->getId()) {
            if (null === $key && null === $value) {
                $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
                if (isset($trace[1]['function'])) { // setSomething
                    $property = lcfirst(substr($trace[1]['function'], 3)); // 3 means length of "set"
                    $getMethod = 'get' . ucfirst($property);
                    if (method_exists($this, $getMethod)) {
                        $this->updates[$property] = $this->$getMethod();
                    }   
                }
            } else {
                $this->updates[$key] = $value;
            }
        }

        return $this;
    }
}
