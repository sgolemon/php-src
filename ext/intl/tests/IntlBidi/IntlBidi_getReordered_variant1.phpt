--TEST--
This tests triggered a U_BUFFER_OVERFLOW_ERROR error during implementation.
--CREDITS--
Jan Slabon <jan.slabon@setasign.com>
--SKIPIF--
<?php if ( !extension_loaded('intl')) print 'skip'; ?>
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
 * Original: https://github.com/friedrich/icu/blob/master/icu4j/main/tests/core/src/com/ibm/icu/dev/test/bidi/TestBidi.java#L497
 */

$bidi = new \IntlBidi();
$bidi->setReorderingMode(\IntlBidi::REORDER_INVERSE_LIKE_DIRECT);
$bidi->setReorderingOptions(\IntlBidi::OPTION_INSERT_MARKS);
$bidi->orderParagraphsLTR(true);

$bidi->setPara("\n\r   \n\rabc\n\u{05d0}\u{05d1}\rabc \u{05d2}\u{05d3}\n\r" .
    "\u{05d4}\u{05d5} abc\n\u{05d6}\u{05d7} abc .-=\r\n" .
    "-* \u{05d8}\u{05d9} abc .-=", \IntlBidi::DEFAULT_RTL);
$expectedResult = "\n\r   \n\rabc\n\u{05d1}\u{05d0}\r\u{05d3}\u{05d2} abc\n\r" .
    "\u{200f}abc \u{05d5}\u{05d4}\n\u{200f}=-. abc \u{05d7}\u{05d6}\r\n" .
    "\u{200f}=-. abc \u{05d9}\u{05d8} *-";
var_dump($bidi->getReordered(0) === $expectedResult);
?>
==DONE==
--EXPECT--
bool(true)
==DONE==