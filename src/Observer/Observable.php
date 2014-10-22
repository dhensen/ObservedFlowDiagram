<?php
namespace Observer;

/**
 * Observable.
 */
class Observable implements ObservableInterface
{

    /**
     * Observer container
     *
     * @var ObserverInterface[][]
     */
    protected $_observers;

    /**
     * @var mixed
     */
    protected $_state;

    /* (non-PHPdoc)
     * @see \Observer\ObservableInterface::attach()
     */
    public function attach($event, ObserverInterface $observer)
    {
        $object_hash = spl_object_hash($observer);
        
        $eventObservers = &$this->_getEventObservers($event);
        
        if (! isset($eventObservers[$object_hash])) {
            $eventObservers[$object_hash] = $observer;
        }
    }

    /* (non-PHPdoc)
     * @see \Observer\ObservableInterface::detach()
     */
    public function detach($event, ObserverInterface $observer)
    {
        $object_hash = spl_object_hash($observer);
        
        $eventObservers = &$this->_getEventObservers($event);
        
        unset($eventObservers[$object_hash]);
    }

    /* (non-PHPdoc)
     * @see \Observer\ObservableInterface::notify()
     */
    public function notify($event)
    {
        $eventObservers = &$this->_getEventObservers($event);
        
        foreach ($eventObservers as $observer) { /* @var $observer ObserverInterface */
            $observer->update($event, $this->getState());
        }
    }

    /**
     * @param string $event
     * @return ObserverInterface[]
     */
    private function &_getEventObservers($event)
    {
        if (! isset($this->_observers[$event])) {
            $this->_observers[$event] = array();
        }
        
        return $this->_observers[$event];
    }

    /* (non-PHPdoc)
     * @see \Observer\ObservableInterface::getState()
     */
    public function getState()
    {
        return $this->_state;
    }

    /**
     *
     * @param mixed $state
     * @return \Observer\Observable
     */
    public function setState($state)
    {
        $this->_state = $state;
        return $this;
    }

    /**
     *
     */
    public function clear()
    {
        $this->_state = null;
        $this->_observers = array();
    }
}