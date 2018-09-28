--TEST--
Regression test for variants to the UBA.
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
 * Original: https://github.com/unicode-org/icu/blob/master/icu4j/main/tests/core/src/com/ibm/icu/dev/test/bidi/TestReorderRunsOnly.java
 */

include 'IntlBidi_ut_common.inc';

$testCases = [
    'abcGHI',
    'a.>67->',
    '-=%$123/ *',
    'abc->12..>JKL',
    'JKL->12..>abc',
    '123->abc',
    '123->JKL',
    '*>12.>34->JKL',
    '*>67.>89->JKL',
    '* /abc-=$%123',
    '* /$%def-=123',
    '-=GHI* /123%$',
    '-=%$JKL* /123',
    'ab =#CD *?450',
    'ab 234 896 de',
    'abc-=%$LMN* /123',
    '123->JKL&MN&P',
    '123'
];

$bidi = new IntlBidi();

$bidi->setReorderingMode(IntlBidi::REORDER_RUNS_ONLY);
$bidi->setReorderingOptions(IntlBidi::OPTION_INSERT_MARKS);

for ($i = 0, $iMax = \count($testCases); $i < $iMax; $i++) {
    $src = $testCases[$i];
    $srcU8 = pseudoToU8($src);

    $bidi->setPara($srcU8, 0);
    var_dump(u8ToPseudo($bidi->getReordered(IntlBidi::DO_MIRRORING)));

    $bidi->setPara($srcU8, 1);
    var_dump(u8ToPseudo($bidi->getReordered(IntlBidi::DO_MIRRORING)));
}

?>
==DONE==
--EXPECT--
string(6) "GHIabc"
string(6) "GHIabc"
string(7) "<-67<.a"
string(7) "<-67<.a"
string(10) "* /%$123=-"
string(10) "* /%$123=-"
string(13) "JKL<..12<-abc"
string(13) "JKL<..abc->12"
string(13) "abc<..JKL->12"
string(13) "abc<..12<-JKL"
string(9) "abc&<-123"
string(8) "abc<-123"
string(8) "JKL<-123"
string(9) "JKL<-@123"
string(13) "JKL<-34<.12<*"
string(14) "JKL<-@34<.12<*"
string(13) "67.>89->JKL<*"
string(13) "67.>89->JKL<*"
string(13) "$%123=-abc/ *"
string(13) "abc-=$%123/ *"
string(13) "123=-def%$/ *"
string(13) "def-=123%$/ *"
string(13) "GHI* /123%$=-"
string(13) "123%$/ *GHI=-"
string(13) "JKL* /%$123=-"
string(13) "123/ *JKL$%=-"
string(13) "CD *?450#= ab"
string(13) "450?* CD#= ab"
string(15) "ab 234 @896@ de"
string(13) "de 896 ab 234"
string(16) "LMN* /%$123=-abc"
string(16) "123/ *LMN$%=-abc"
string(11) "JKLMNP<-123"
string(12) "JKLMNP<-@123"
string(3) "123"
string(3) "123"
==DONE==