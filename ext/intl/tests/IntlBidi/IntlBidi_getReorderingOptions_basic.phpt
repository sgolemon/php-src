--TEST--
Test the getter and setter, to make sure that it stores the options.
--CREDITS--
Timo Scholz <timo.scholz@setasign.com>
--SKIPIF--
<?php if (!extension_loaded('intl')) print 'skip'; ?>
--FILE--
<?php
$bidi = new IntlBidi();

var_dump($bidi->getReorderingOptions());
$bidi->setReorderingOptions(IntlBidi::OPTION_STREAMING);
var_dump($bidi->getReorderingOptions());
$bidi->setReorderingOptions(IntlBidi::OPTION_INSERT_MARKS);
var_dump($bidi->getReorderingOptions());
?>
==DONE==
--EXPECT--
int(0)
int(4)
int(1)
==DONE==