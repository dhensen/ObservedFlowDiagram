<?php

namespace Observer;

class Observable implements ObservableInterface
{
	/**
	 * Observer container
	 * @var ObserverInterface[][]
	 */
	protected $_observers;

	public function attach($event, ObserverInterface $observer)
	{
		$object_hash = spl_object_hash($observer);

		$eventObservers = &$this->_getEventObservers($event);

		if (!isset($eventObservers[$object_hash])) {
			$eventObservers[$object_hash] = $observer;
		}
	}

	public function detach($event, ObserverInterface $observer)
	{
		$object_hash = spl_object_hash($observer);

		$eventObservers = &$this->_getEventObservers($event);

		unset($eventObservers[$object_hash]);
	}

	public function notify($event)
	{
		$eventObservers = &$this->_getEventObservers($event);

		foreach ($eventObservers as $observer) { /* @var $observer ObserverInterface */
			$observer->update();
		}
	}

	private function &_getEventObservers($event)
	{
		if (!isset($this->_observers[$event])) {
			$this->_observers[$event] = array();
		}

		return $this->_observers[$event];
	}
}