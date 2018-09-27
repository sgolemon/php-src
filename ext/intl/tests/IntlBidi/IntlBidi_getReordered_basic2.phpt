--TEST--
Regression test for the UBA implementation.
--CREDITS--
Timo Scholz <timo.scholz@setasign.com>
--SKIPIF--
<?php if ( !extension_loaded('intl') ) print 'skip'; ?>
<?php if ( !extension_loaded('mbstring')) print 'skip'; ?>
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

include 'IntlBidi_ut_common.inc';

// --- INIT TEST DATA ---

$logicalOrder = [
    'del(KC)add(K.C.&)',
    'del(QDVT) add(BVDL)',
    'del(PQ)add(R.S.)T)U.&',
    'del(LV)add(L.V.) L.V.&',
    'day  0  R  DPDHRVR dayabbr',
    'day  1  H  DPHPDHDA dayabbr',
    'day  2   L  DPBLENDA dayabbr',
    'day  3  J  DPJQVM  dayabbr',
    'day  4   I  DPIQNF    dayabbr',
    'day  5  M  DPMEG  dayabbr',
    'helloDPMEG',
    'hello WXY'
];

// --- RUN TEST ---

$nTests = \count($logicalOrder);
for ($testNumber = 0; $testNumber < $nTests; $testNumber++) {

    // prepare the source.
    $src = $logicalOrder[$testNumber];
    $srcUt8 = pseudoToU8($src);

    $bidi = new \IntlBidi();
    $bidi->setInverse(true);
    $bidi->setPara($srcUt8, \IntlBidi::DEFAULT_LTR);
    $result = u8ToPseudo($bidi->getReordered(\IntlBidi::OUTPUT_REVERSE | \IntlBidi::INSERT_LRM_FOR_NUMERIC));
    var_dump($result);
}
?>
==DONE==
--EXPECT--
string(22) "@)@K.C.&@(dda)@KC@(led"
string(24) "@)@BVDL@(dda )@QDVT@(led"
string(24) "R.S.)T)U.&@(dda)@PQ@(led"
string(25) "L.V.) L.V.&@(dda)@LV@(led"
string(28) "rbbayad @R  DPDHRVR@  0  yad"
string(29) "rbbayad @H  DPHPDHDA@  1  yad"
string(30) "rbbayad @L  DPBLENDA@   2  yad"
string(28) "rbbayad  @J  DPJQVM@  3  yad"
string(31) "rbbayad    @I  DPIQNF@   4  yad"
string(27) "rbbayad  @M  DPMEG@  5  yad"
string(10) "DPMEGolleh"
string(10) "WXY@ olleh"
==DONE==