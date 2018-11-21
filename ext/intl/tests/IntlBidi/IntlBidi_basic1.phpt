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
    $bidi->setInverse(true);
    $levels = str_repeat("\0", \IntlBidi::MAX_EXPLICIT_LEVEL);
    for ($i = 0; $i < 10; $i++) {
        $levels[$i] = chr($i + 1);
    }
    $bidi->setPara($srcUt8, \IntlBidi::DEFAULT_LTR, $levels);
    $result = u8ToPseudo($bidi->getReordered(\IntlBidi::DO_MIRRORING | \IntlBidi::REMOVE_BIDI_CONTROLS));
    var_dump($result);
}
?>
==DONE==
--EXPECT--
string(16) "del(add(CK(.C.K)"
string(19) "del( (TVDQadd(LDVB)"
string(20) "del(add(QP(.U(T(.S.R"
string(21) "del(add(VL(.V.L (.V.L"
string(26) "day 0  R   RVRHDPD dayabbr"
string(27) "day 1  H   ADHDPHPD dayabbr"
string(28) "day 2 L     ADNELBPD dayabbr"
string(26) "day 3  J   MVQJPD  dayabbr"
string(29) "day 4 I     FNQIPD    dayabbr"
string(25) "day 5  M   GEMPD  dayabbr"
string(10) "helloGEMPD"
string(9) "hello YXW"
==DONE==