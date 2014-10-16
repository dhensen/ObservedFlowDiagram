<?php

namespace Observer;

abstract class Observer implements ObserverInterface
{
	abstract public function update();
}