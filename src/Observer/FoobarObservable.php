<?php

namespace Observer;

class FoobarObservable extends Observable
{
	public function somethingGetsUpdated()
	{
		// update something
		$this->notify('update');
	}
}