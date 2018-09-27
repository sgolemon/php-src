--TEST--
Test the getter and setter, to make sure that it stores the mode.
--CREDITS--
Timo Scholz <timo.scholz@setasign.com>
--SKIPIF--
<?php if (!extension_loaded('intl')) print 'skip'; ?>
--FILE--
<?php
$bidi = new IntlBidi();

var_dump($bidi->getReorderingMode());
$bidi->setReorderingMode(IntlBidi::REORDER_NUMBERS_SPECIAL);
var_dump($bidi->getReorderingMode());
$bidi->setReorderingMode(IntlBidi::REORDER_RUNS_ONLY);
var_dump($bidi->getReorderingMode());
?>
==DONE==
--EXPECT--
int(0)
int(1)
int(3)
==DONE==