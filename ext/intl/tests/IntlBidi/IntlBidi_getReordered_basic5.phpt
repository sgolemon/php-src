--TEST--
Test inverse Bidi with marks and contextual orientation.
--CREDITS--
Timo Scholz <timo.scholz@setasign.com>, Jan Slabon <jan.slabon@setasign.com>
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
 * Original: https://github.com/friedrich/icu/blob/master/icu4j/main/tests/core/src/com/ibm/icu/dev/test/bidi/TestBidi.java#L473
 */

include 'IntlBidi_ut_common.inc';

$bidi = new \IntlBidi();
$bidi->setReorderingMode(\IntlBidi::REORDER_INVERSE_LIKE_DIRECT);
$bidi->setReorderingOptions(\IntlBidi::OPTION_INSERT_MARKS);

$bidi->setPara('');
var_dump($bidi->getReordered(0) === '');

$bidi->setPara('   ', \IntlBidi::DEFAULT_RTL);
var_dump($bidi->getReordered(0) === '   ');

$bidi->setPara("abc", \IntlBidi::DEFAULT_RTL);
var_dump($bidi->getReordered(0) === 'abc');

$bidi->setPara("\u{05d0}\u{05d1}", \IntlBidi::DEFAULT_RTL);
var_dump($bidi->getReordered(0) === "\u{05d1}\u{05d0}");

$bidi->setPara("abc \u{05d0}\u{05d1}", \IntlBidi::DEFAULT_RTL);
var_dump($bidi->getReordered(0) === "\u{05d1}\u{05d0} abc");

$bidi->setPara("\u{05d0}\u{05d1} abc", \IntlBidi::DEFAULT_RTL);
var_dump($bidi->getReordered(0) === "\u{200f}abc \u{05d1}\u{05d0}");

$bidi->setPara("\u{05d0}\u{05d1} abc .-=", \IntlBidi::DEFAULT_RTL);
var_dump($bidi->getReordered(0) === "\u{200f}=-. abc \u{05d1}\u{05d0}");

$bidi->orderParagraphsLTR(true);

// This had raised a BUFFER OVERFLOW ERROR: see IntlBidi_getReordered_variant1.phpt
 $bidi->setPara("\n\r   \n\rabc\n\u{05d0}\u{05d1}\rabc \u{05d2}\u{05d3}\n\r" .
    "\u{05d4}\u{05d5} abc\n\u{05d6}\u{05d7} abc .-=\r\n" .
    "-* \u{05d8}\u{05d9} abc .-=", \IntlBidi::DEFAULT_RTL);
$expectedResult = "\n\r   \n\rabc\n\u{05d1}\u{05d0}\r\u{05d3}\u{05d2} abc\n\r" .
    "\u{200f}abc \u{05d5}\u{05d4}\n\u{200f}=-. abc \u{05d7}\u{05d6}\r\n" .
    "\u{200f}=-. abc \u{05d9}\u{05d8} *-";
var_dump($bidi->getReordered(0) === $expectedResult);

$bidi->setPara("\u{05d0} \t", \IntlBidi::LTR);
var_dump($bidi->getReordered(0) === "\u{05d0}\u{200e} \t");

// This had raised a BUFFER OVERFLOW ERROR: see IntlBidi_getReordered_variant3.phpt
$bidi->setPara("\u{05d0} 123 \t\u{05d1} 123 \u{05d2}", \IntlBidi::LTR);
var_dump($bidi->getReordered(0) === "\u{05d0} \u{200e}123\u{200e} \t\u{05d2} 123 \u{05d1}");

// This had raised a BUFFER OVERFLOW ERROR: see IntlBidi_getReordered_variant4.phpt
$bidi->setPara("\u{05d0} 123 \u{0660}\u{0661} ab", \IntlBidi::LTR);
var_dump($bidi->getReordered(0) === "\u{05d0} \u{200e}123 \u{200e}\u{0660}\u{0661} ab");

$bidi->setPara("ab \t", \IntlBidi::RTL);
var_dump($bidi->getReordered(0) === "\u{200f}\t ab");

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
bool(true)
bool(true)
bool(true)
bool(true)
==DONE==