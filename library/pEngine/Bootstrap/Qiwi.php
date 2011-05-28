<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yura
 * Date: 10.05.11
 * Time: 15:01
 * To change this template use File | Settings | File Templates.
 */
 
class pEngine_Bootstrap_Qiwi extends Zend_Application_Resource_ResourceAbstract{

    /**
     * @var \SplObjectStorage
     */
    protected $storage;

    protected $event = null;

    /**
     * Strategy pattern: initialize resource
     *
     * @return pEngine_Bootstrap_Qiwi
     */

    public function init()
    {
        $this->storage = new SplObjectStorage();
        return $this;
    }


    public function attach(pEngine_Qiwi_Observer $observer)
    {
        $this->storage->attach($observer);
    }


    public function detach(pEngine_Qiwi_Observer $observer)
    {
        $this->storage->detach($observer);
    }


    public function notify()
    {
        foreach($this->storage as $obj)
        {
            $obj->update($this->event);
        }
    }

    public function setEvent($eventType,$eventData){
        $event = new stdClass();
        $event->type = $eventType;
        $event->data = $eventData;
        
        $this->event = $event;

        $this->notify();
    }
}