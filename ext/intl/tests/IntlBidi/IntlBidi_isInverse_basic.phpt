--TEST--
Test the getter and setter, to make sure that it stores the inverse flag.
--CREDITS--
Timo Scholz <timo.scholz@setasign.com>
--SKIPIF--
<?php if (!extension_loaded('intl')) print 'skip'; ?>
--FILE--
<?php
$bidi = new IntlBidi();

var_dump($bidi->isInverse());
$bidi->setInverse(false);
var_dump($bidi->isInverse());
$bidi->setInverse(true);
var_dump($bidi->isInverse());
$bidi->setInverse(false);
$bidi->setReorderingMode(IntlBidi::REORDER_INVERSE_NUMBERS_AS_L); // 4
var_dump($bidi->isInverse());
$bidi->setInverse(false); // set the flag and the value to 0
var_dump($bidi->isInverse());
var_dump($bidi->getReorderingMode()); // should be 0, since the flag got reset by setInverse
?>
==DONE==
--EXPECT--
bool(false)
bool(false)
bool(true)
bool(true)
bool(false)
int(0)
==DONE==