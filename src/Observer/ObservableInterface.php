<?php

namespace Observer;

interface ObservableInterface
{
	public function attach($event, ObserverInterface $observer);
	public function detach($event, ObserverInterface $observer);
	public function notify($event);
}