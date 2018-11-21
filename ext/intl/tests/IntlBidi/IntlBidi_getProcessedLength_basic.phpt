--TEST--
Test IntlBidi::getProcessedLength() method.
--CREDITS--
Jan Slabon <jan.slabon@setasign.com>
--SKIPIF--
<?php if (!extension_loaded('intl')) print 'skip'; ?>
<?php if (!extension_loaded('mbstring')) print 'skip'; ?>
--FILE--
<?php

// include helper functions
include 'IntlBidi_ut_common.inc';

$bidi = new \IntlBidi();

$bidi->setPara('testen');
var_dump($bidi->getProcessedLength()); // 6
// 'del(KC)add(K.C.&)'

$string = pseudoToU8('del(KC)');
$bidi->setPara($string);
var_dump($bidi->getProcessedLength()); // 7

$string = pseudoToU8('(K.C.&)');
$bidi->setPara($string);
var_dump($bidi->getProcessedLength()); // 7


$bidi->setReorderingOptions(\IntlBidi::OPTION_STREAMING);
// meaningful boundary
$bidi->setPara('abcdefghijklmnopqrstuvwxyz');
var_dump($bidi->getProcessedLength()); // 0

$string = "testen\ntesten\ntesten";
$len = strlen($string);
$bidi->setPara($string);
var_dump($bidi->getProcessedLength()); // 14
// turn streaming off before getting the last part of the text
$bidi->setReorderingOptions(\IntlBidi::OPTION_DEFAULT);
$bidi->setPara(substr($string, 14, $len - 14));
var_dump($bidi->getProcessedLength()); // 6

?>
==DONE==
--EXPECT--
int(6)
int(7)
int(7)
int(0)
int(14)
int(6)
==DONE==