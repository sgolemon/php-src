--TEST--
Test get result length.
--CREDITS--
Timo Scholz <timo.scholz@setasign.com>
--SKIPIF--
<?php if (!extension_loaded('intl')) print 'skip'; ?>
--FILE--
<?php
$bidi = new IntlBidi(120, 66);

$str = "\u{200e}" . 'abc       def';
$bidi->setPara($str, IntlBidi::RTL);
$bidi->setReorderingOptions(IntlBidi::REMOVE_BIDI_CONTROLS);

var_dump($bidi->getResultLength());
$bidiLine2 = $bidi->setLine(0, 6);
var_dump($bidi->getResultLength());
var_dump($bidiLine2->getResultLength());

unset($bidiLine2);
gc_collect_cycles();

var_dump($bidi->getResultLength());
unset($bidi);
gc_collect_cycles();

?>
==DONE==
--EXPECT--
int(14)
int(14)
int(5)
int(14)
==DONE==