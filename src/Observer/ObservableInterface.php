<?php
namespace Observer;

/**
 * ObservableInterface
 */
interface ObservableInterface
{
    /**
     * Attach/subscribe observer to event.
     *
     * @param string $event
     * @param ObserverInterface $observer
     */
    public function attach($event, ObserverInterface $observer);

    /**
     * Detach/unsubscribe observer from event.
     *
     * @param string $event
     * @param ObserverInterface $observer
     */
    public function detach($event, ObserverInterface $observer);

    /**
     * Notifies all attached observers of occurring event.
     *
     * @param string $event
     */
    public function notify($event);

    /**
     * Returns the observable state.
     *
     * @return mixed
     */
    public function getState();
}