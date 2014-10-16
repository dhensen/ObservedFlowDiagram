<?php

require_once 'vendor/autoload.php';

use FlowDiagram\Block;

$blockA = Block::create('A');

$blockB = Block::create('B');
$blockB->setOperation(function ($in) {
	if ($in === 'foobar') {
		return 'barfoo';
	}
});

$blockC = Block::create('C');


/*  Output of C is input for A and B:

	 ----A
	/
	C----B

 */

$blockC->input('foobar');

$blockC->predecessorTo($blockA);
$blockC->predecessorTo($blockB);

d($blockA->output());
d($blockB->output());

$blockB->remove();
unset($blockB);



d((memory_get_peak_usage(true) / 1024) . ' KiB');