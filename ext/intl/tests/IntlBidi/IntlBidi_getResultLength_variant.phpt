--TEST--
This has wrong behaviour, since the bidi object of $bidi gets deleted, but actually should stay alive until $bidiLine2 gets deleted or $bidiLine2->setPara() gets called.
Or we return a completely different instance, just to make it easier to handle. (But this would disable "stacking" of the setLine() call.
Also we should change the name to getLine().
http://icu-project.org/apiref/icu4c/ubidi_8h.html#a88693e5a8ad4be974dc90ec6b8db56df
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

unset($bidi);

gc_collect_cycles();
gc_collect_cycles();

var_dump($bidiLine2->getResultLength());
?>
==DONE==
--EXPECT--
int(14)
int(14)
int(5)
==DONE==