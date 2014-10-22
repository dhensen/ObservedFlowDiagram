<?php

require_once __DIR__ . '/../vendor/autoload.php';

class FoobarObservable extends Observer\Observable
{
    /**
     * This dummy supposedly updates something after which observers are notified of this.
     */
    public function updateSomething()
    {
        // update something
        $this->notify('update');
    }
}

class FoobarObserver extends Observer\Observer
{
    /**
     * Perform the action when update is triggered by obserable
     *
     * @param string $event
     * @param mixed $state
     */
    public function update($event, $state)
    {
        echo 'something got updated!!';
    }
}

// create observable
$foobarObservable = new FoobarObservable();

// update something
$foobarObservable->updateSomething();

// nothing else happens


// create an observer
$foobarObserver = new FoobarObserver();
// attach it to the update event on the foobarObserver
$foobarObservable->attach('update', $foobarObserver);

// update something
$foobarObservable->updateSomething();


// foobarServer update runs

// detach the foobarServer from the foobarObserable
$foobarObservable->detach('update', $foobarObserver);

// update something
$foobarObservable->updateSomething();

// nothing happens

d((memory_get_peak_usage(true) / 1024) . ' KiB');