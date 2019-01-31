--TEST--
Test inverse behavior
--CREDITS--
Timo Scholz <timo.scholz@setasign.com>, Jan Slabon <jan.slabon@setasign.com>
--SKIPIF--
<?php if (!extension_loaded('intl')) print 'skip'; ?>
<?php if (!extension_loaded('mbstring')) print 'skip'; ?>
--FILE--
<?php
// include helper functions
include 'IntlBidi_ut_common.inc';

$bidi = new \IntlBidi();

// without options
$original = pseudoToU8('(KC)add(K.C.&)');
$bidi->setPara($original);
$result = $bidi->getReordered(0);
var_dump(u8ToPseudo($result) === ")&.C.K(add)CK(");

$bidi->setPara($result);
$bidi->setInverse(true);
$result = $bidi->getReordered(0);
var_dump($original === $result);

// \IntlBidi::DO_MIRRORING
$bidi->setInverse(false);
$bidi->setPara($original);
$result = $bidi->getReordered(\IntlBidi::DO_MIRRORING);
var_dump(u8ToPseudo($result) === "(&.C.K)add(CK)");

$bidi->setPara($result);
$bidi->setInverse(true);
$result = $bidi->getReordered(\IntlBidi::DO_MIRRORING);
var_dump($original === $result);

// \IntlBidi::OUTPUT_REVERSE
$bidi->setInverse(false);
$bidi->setPara($original);
$result = $bidi->getReordered(\IntlBidi::OUTPUT_REVERSE);
var_dump(u8ToPseudo($result) === "(KC)dda(K.C.&)");

$bidi->setPara($result);
$bidi->setInverse(true);
$result = $bidi->getReordered(\IntlBidi::OUTPUT_REVERSE);
var_dump($original === $result);

// \IntlBidi::DO_MIRRORING | \IntlBidi::OUTPUT_REVERSE
$bidi->setInverse(false);
$bidi->setPara($original);
$result = $bidi->getReordered(\IntlBidi::DO_MIRRORING | \IntlBidi::OUTPUT_REVERSE);
var_dump(u8ToPseudo($result) === ")KC(dda)K.C.&(");

$bidi->setPara($result);
$bidi->setInverse(true);
$result = $bidi->getReordered(\IntlBidi::DO_MIRRORING | \IntlBidi::OUTPUT_REVERSE);
var_dump($original === $result);

?>
==DONE==
--EXPECT--
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
==DONE==