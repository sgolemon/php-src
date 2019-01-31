--TEST--
Test the getter and setter, to make sure that it stores the isOrderParagraphsLTR flag.
--CREDITS--
Timo Scholz <timo.scholz@setasign.com>
--SKIPIF--
<?php if (!extension_loaded('intl')) print 'skip'; ?>
--FILE--
<?php
$bidi = new IntlBidi();

var_dump($bidi->isOrderParagraphsLTR());
$bidi->orderParagraphsLTR(true);
var_dump($bidi->isOrderParagraphsLTR());
$bidi->orderParagraphsLTR(false);
var_dump($bidi->isOrderParagraphsLTR());
?>
==DONE==
--EXPECT--
bool(false)
bool(true)
bool(false)
==DONE==