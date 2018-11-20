--TEST--
Test for IntlBidi getBaseDirection to verify string direction detection function.
--CREDITS--
Timo Scholz
<timo.scholz@setasign.com>
--SKIPIF--
<?php if (!extension_loaded('intl')) print 'skip'; ?>
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
 * Original: https://github.com/unicode-org/icu/blob/778d0a6d1d46faa724ead19613bda84621794b72/icu4j/main/tests/core/src/com/ibm/icu/dev/test/bidi/TestBidi.java#L531
 */
$bidi = new \IntlBidi();

// mixed start with L
var_dump($bidi->getBaseDirection("\u{0061}\u{0627}\u{0032}\u{06f3}\u{0061}\u{0034}") === \IntlBidi::LTR);
// mixed start with AL
var_dump($bidi->getBaseDirection( "\u{0661}\u{0627}\u{0662}\u{06f3}\u{0061}\u{0664}") === \IntlBidi::RTL);

// mixed Start with R
var_dump($bidi->getBaseDirection("\u{05EA}\u{0627}\u{0662}\u{06f3}\u{0061}\u{0664}") === \IntlBidi::RTL);

// all AL (Arabic. Persian)
var_dump($bidi->getBaseDirection("\u{0698}\u{067E}\u{0686}\u{06AF}") === \IntlBidi::RTL);

// all R (Hebrew etc.)
var_dump($bidi->getBaseDirection("\u{0590}\u{05D5}\u{05EA}\u{05F1}") === \IntlBidi::RTL);

// all L (English)
var_dump($bidi->getBaseDirection("\u{0071}\u{0061}\u{0066}") === \IntlBidi::LTR);

// mixed start with weak AL an then L
var_dump($bidi->getBaseDirection("\u{0663}\u{0071}\u{0061}\u{0066}") === \IntlBidi::LTR);

// mixed start with weak L and then AL */
var_dump($bidi->getBaseDirection("\u{0031}\u{0698}\u{067E}\u{0686}\u{06AF}") === \IntlBidi::RTL);

// empty
var_dump($bidi->getBaseDirection('') === \IntlBidi::NEUTRAL);

// all weak L (English digits)
var_dump($bidi->getBaseDirection("\u{0031}\u{0032}\u{0033}") === \IntlBidi::NEUTRAL);

// all weak AL (Arabic digits)
var_dump($bidi->getBaseDirection("\u{0663}\u{0664}\u{0665}") === \IntlBidi::NEUTRAL);

// first L (English) others are R (Hebrew etc.)
var_dump($bidi->getBaseDirection("\u{0071}\u{0590}\u{05D5}\u{05EA}\u{05F1}") === \IntlBidi::LTR);

// last R (Hebrew etc.) others are weak L (English Digits)
var_dump($bidi->getBaseDirection("\u{0031}\u{0032}\u{0033}\u{05F1}") === \IntlBidi::RTL);
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
bool(true)
==DONE==