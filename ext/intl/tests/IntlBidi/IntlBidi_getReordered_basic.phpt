--TEST--
Regression test for the UBA implementation.
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
    $bidi->setPara($srcUt8, \IntlBidi::DEFAULT_LTR);
    $result = u8ToPseudo($bidi->getReordered(\IntlBidi::DO_MIRRORING));
    var_dump($result);
}
?>
==DONE==
--EXPECT--
string(17) "del(CK)add(&.C.K)"
string(19) "del(TVDQ) add(LDVB)"
string(21) "del(QP)add(S.R.)&.U(T"
string(22) "del(VL)add(V.L.) &.V.L"
string(26) "day  0  RVRHDPD  R dayabbr"
string(27) "day  1  ADHDPHPD  H dayabbr"
string(28) "day  2   ADNELBPD  L dayabbr"
string(26) "day  3  MVQJPD  J  dayabbr"
string(29) "day  4   FNQIPD  I    dayabbr"
string(25) "day  5  GEMPD  M  dayabbr"
string(10) "helloGEMPD"
string(9) "hello YXW"
==DONE==