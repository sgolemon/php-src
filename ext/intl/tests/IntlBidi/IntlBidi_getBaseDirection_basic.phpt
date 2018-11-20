+--TEST--
Test for IntlBidi getBaseDirection to verify string direction detection function.
This currently fails, i still need to check if the test has the wrong implementation or if Bidi is giving faulty results.
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
$bidi = new IntlBidi();

//mixed english
var_dump($bidi->getBaseDirection("\u{0061}\u{0627}\u{0032}\u{06f3}\u{0061}\u{0034}"));
// arabic mixed
var_dump($bidi->getBaseDirection( "\u{0661}\u{0627}\u{0662}\u{06f3}\u{0061}\u{0664}"));
// hebrew mixed
var_dump($bidi->getBaseDirection("\u{05EA}\u{0627}\u{0662}\u{06f3}\u{0061}\u{0664}"));
// persian
var_dump($bidi->getBaseDirection("\u{0698}\u{067E}\u{0686}\u{06AF}"));
// hebrew
var_dump($bidi->getBaseDirection("\u{0590}\u{05D5}\u{05EA}\u{05F1}"));
// english
var_dump($bidi->getBaseDirection("\u{0071}\u{0061}\u{0066}"));

// empty
var_dump($bidi->getBaseDirection(''));
// all wait Al (English digits)
var_dump($bidi->getBaseDirection("\u{0031}\u{0032}\u{0033}"));
// all weak Al (Arabic digits)
var_dump($bidi->getBaseDirection("\u{0663}\u{0664}\u{0665}"));
// english and hebrew
var_dump($bidi->getBaseDirection("\u{0071}\u{0590}\u{05D5}\u{05EA}\u{05F1}"));
// all english and last hebrew
var_dump($bidi->getBaseDirection("\u{0031}\u{0032}\u{0033}\u{05F1}"));
?>
==DONE==
--EXPECT--
int(0)
int(1)
int(1)
int(1)
int(1)
int(0)
int(2)
int(2)
int(2)
int(0)
int(1)
==DONE==