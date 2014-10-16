<?php

require_once 'vendor/autoload.php';

// create observable
$foobarObservable = new Observer\FoobarObservable();

// update something
$foobarObservable->somethingGetsUpdated();

// nothing else happens


// create an observer
$foobarObserver = new Observer\FoobarObserver();
// attach it to the update event on the foobarObserver
$foobarObservable->attach('update', $foobarObserver);

// update something
$foobarObservable->somethingGetsUpdated();


// foobarServer update runs

// detach the foobarServer from the foobarObserable
$foobarObservable->detach('update', $foobarObserver);

// update something
$foobarObservable->somethingGetsUpdated();

// nothing happens

d((memory_get_peak_usage(true) / 1024) . ' KiB');