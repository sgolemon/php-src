--TEST--
Regression test for doing transformations in context.
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
 * Original: https://github.com/unicode-org/icu/blob/master/icu4j/main/tests/core/src/com/ibm/icu/dev/test/bidi/TestContext.java
 */
include 'IntlBidi_ut_common.inc';

$data = [
    /*00*/  ['', '', '', \IntlBidi::LTR],
    /*01*/  ['', '.-=JKL-+*', '', \IntlBidi::LTR],
    /*02*/  [' ', '.-=JKL-+*', ' ', \IntlBidi::LTR],
    /*03*/  ['a', '.-=JKL-+*', 'b', \IntlBidi::LTR],
    /*04*/  ['D', '.-=JKL-+*', '', \IntlBidi::LTR],
    /*05*/  ['', '.-=JKL-+*', ' D', \IntlBidi::LTR],
    /*06*/  ['', '.-=JKL-+*', ' 2', \IntlBidi::LTR],
    /*07*/  ['', '.-=JKL-+*', ' 7', \IntlBidi::LTR],
    /*08*/  [' G 1', '.-=JKL-+*', ' H', \IntlBidi::LTR],
    /*09*/  ['7', '.-=JKL-+*', ' H', \IntlBidi::LTR],
    /*10*/  ['', '.-=abc-+*', '', \IntlBidi::RTL],
    /*11*/  [' ', '.-=abc-+*', ' ', \IntlBidi::RTL],
    /*12*/  ['D', '.-=abc-+*', 'G', \IntlBidi::RTL],
    /*13*/  ['x', '.-=abc-+*', '', \IntlBidi::RTL],
    /*14*/  ['', '.-=abc-+*', ' y', \IntlBidi::RTL],
    /*15*/  ['', '.-=abc-+*', ' 2', \IntlBidi::RTL],
    /*16*/  [' x 1', '.-=abc-+*', ' 2', \IntlBidi::RTL],
    /*17*/  [' x 7', '.-=abc-+*', ' 8', \IntlBidi::RTL],
    /*18*/  ['x|', '.-=abc-+*', ' 8', \IntlBidi::RTL],
    /*19*/  ['G|y', '.-=abc-+*', ' 8', \IntlBidi::RTL],
    /*20*/  ['', '.-=', '', \IntlBidi::DEFAULT_LTR],
    /*21*/  ['D', '.-=', '', \IntlBidi::DEFAULT_LTR],
    /*22*/  ['G', '.-=', '', \IntlBidi::DEFAULT_LTR],
    /*23*/  ['xG', '.-=', '', \IntlBidi::DEFAULT_LTR],
    /*24*/  ['x|G', '.-=', '', \IntlBidi::DEFAULT_LTR],
    /*25*/  ['x|G', '.-=|-+*', '', \IntlBidi::DEFAULT_LTR],
];

$bidi = new IntlBidi();

$bidi->orderParagraphsLTR(true);

for ($i = 0, $iMax = \count($data); $i < $iMax; $i++) {
    [$prologue, $src, $epilogue, $paraLevel] = $data[$i];

    $prologue = pseudoToU8($prologue);
    $epilogue = pseudoToU8($epilogue);
    $src = pseudoToU8($src);

    $bidi->setContext($epilogue, $prologue);
    $bidi->setContext($prologue, $epilogue);

    $bidi->setPara($src, $paraLevel);
    var_dump(u8ToPseudo($bidi->getReordered(\IntlBidi::DO_MIRRORING)));
}
?>
==DONE==
--EXPECT--
string(0) ""
string(9) ".-=LKJ-+*"
string(9) ".-=LKJ-+*"
string(9) ".-=LKJ-+*"
string(9) "LKJ=-.-+*"
string(9) ".-=*+-LKJ"
string(9) ".-=*+-LKJ"
string(9) ".-=*+-LKJ"
string(9) "*+-LKJ=-."
string(9) ".-=*+-LKJ"
string(9) "*+-abc=-."
string(9) "*+-abc=-."
string(9) "*+-abc=-."
string(9) "*+-.-=abc"
string(9) "abc-+*=-."
string(9) "abc-+*=-."
string(9) ".-=abc-+*"
string(9) "*+-.-=abc"
string(9) "*+-abc=-."
string(9) "*+-.-=abc"
string(3) ".-="
string(3) "=-."
string(3) "=-."
string(3) ".-="
string(3) "=-."
string(7) "=-.|-+*"
==DONE==