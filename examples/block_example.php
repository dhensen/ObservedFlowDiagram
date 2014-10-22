<?php

require_once __DIR__ . '/../vendor/autoload.php';

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

$blockA1 = Block::create('A1');
$blockB1 = Block::create('B1');
$blockC1 = Block::create('C1');

$blockA1->predecessorTo($blockC1);
$blockB1->predecessorTo($blockC1);

$blockB1->input(10);
$blockA1->input(2);
$blockC1->setOperation(function ($a, $b) { return $a * $b; });
d($blockC1->output());


// following not yet working (dude, convert to phpunit test asap)
$blockA1->input(100);
d($blockC1->output());

d((memory_get_peak_usage(true) / 1024) . ' KiB');