--TEST--
Test getReordered with setLine and multiple layers.
--CREDITS--
Timo Scholz <timo.scholz@setasign.com>
--SKIPIF--
<?php if (!extension_loaded('intl')) print 'skip'; ?>
<?php if (!extension_loaded('mbstring')) print 'skip'; ?>
--FILE--
<?php
/**
 * © 2016 and later: Unicode, Inc. and others.
 * License & terms of use: http://www.unicode.org/copyright.html#License
 *
 *******************************************************************************
 *   Copyright (C) 2001-2013, International Business Machines
 *   Corporation and others.  All Rights Reserved.
 *******************************************************************************
 */
/**
 * Ported from Java.
 * Original: https://github.com/unicode-org/icu/blob/master/icu4j/main/tests/core/src/com/ibm/icu/dev/test/bidi/TestReorder.java
 */

// include helper functions
include 'IntlBidi_ut_common.inc';

// --- INIT TEST DATA ---

$logicalOrder = 'del(KC)add(K.C.&)';


// --- RUN TEST ---

// prepare the source.
$srcUt8 = pseudoToU8($logicalOrder);

 $bidi = new \IntlBidi();
$bidi->setPara($srcUt8, \IntlBidi::DEFAULT_LTR);
$newBidi = $bidi->setLine(0, $bidi->getResultLength());
$newBidi2 = $newBidi->setLine(0, $newBidi->getResultLength());
$newBidi3 = $newBidi2->setLine(0, $newBidi2->getResultLength());

// $bidi->setPara('', \Intlbidi::DEFAULT_LTR);

$result = u8ToPseudo($newBidi3->getReordered(\IntlBidi::DO_MIRRORING));
var_dump($result);
?>
==DONE==
--EXPECT--
string(17) "del(CK)add(&.C.K)"
==DONE==