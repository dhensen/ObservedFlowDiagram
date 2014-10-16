<?php

namespace Observer;

class FoobarObserver extends Observer
{
	public function update()
	{
		echo 'something got updated!!';
	}
}