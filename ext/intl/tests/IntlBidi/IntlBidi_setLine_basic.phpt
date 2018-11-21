--TEST--
Simple test for setLine
--CREDITS--
Timo Scholz
<timo.scholz@setasign.com>
--SKIPIF--
<?php if (!extension_loaded('intl')) print 'skip'; ?>
--FILE--
<?php
$bidi = new \IntlBidi();
$bidi->setPara('abcde');

$line = $bidi->setLine(0, 2);
var_dump($line->getReordered(0));

$line = $bidi->setLine(0, 1);
var_dump($line->getReordered(0));

?>
==DONE==
--EXPECT--
string(2) "ab"
string(1) "a"
==DONE==

