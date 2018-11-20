--TEST--
Test for IntlBidi getLevelAt
--CREDITS--
Timo Scholz
<timo.scholz@setasign.com>
--SKIPIF--
<?php if (!extension_loaded('intl')) print 'skip'; ?>
--FILE--
<?php

$bidi = new \IntlBidi();

$bidi->setPara("abc          ", \IntlBidi::RTL, null);
$bidiLine = $bidi->setLine(0, 6);
for ($i = 3; $i < 6; $i++) {
    var_dump($bidiLine->getLevelAt($i));
}

$bidi->setPara("abc       def", \IntlBidi::RTL, null);
$bidiLine = $bidi->setLine(0, 6);
for ($i = 3; $i < 6; $i++) {
    var_dump($bidiLine->getLevelAt($i));
}

$bidi->setPara("abcdefghi    ", \IntlBidi::RTL, null);
$bidiLine = $bidi->setLine(0, 6);
for ($i = 3; $i < 6; $i++) {
    var_dump($bidiLine->getLevelAt($i));
}

?>
==DONE==
--EXPECT--
bool(0)
bool(0)
bool(0)
bool(1)
bool(1)
bool(1)
bool(2)
bool(2)
bool(2)
==DONE==