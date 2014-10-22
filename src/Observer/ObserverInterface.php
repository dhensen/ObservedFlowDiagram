<?php
namespace Observer;

/**
 * ObserverInterface
 */
interface ObserverInterface
{
    /**
     * Runs when invoked by an occuring event for an Observable to which this Observer is attached
     *
     * @param string $event
     * @param mixed $state
     */
    public function update($event, $state);
}