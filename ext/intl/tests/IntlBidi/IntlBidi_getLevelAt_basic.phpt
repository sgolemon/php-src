--TEST--
Test for IntlBidi getLevelAt
--CREDITS--
Timo Scholz <timo.scholz@setasign.com>, Jan Slabon <jan.slabon@setasign.com>
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
 * Original: https://github.com/unicode-org/icu/blob/778d0a6d1d46faa724ead19613bda84621794b72/icu4j/main/tests/core/src/com/ibm/icu/dev/test/bidi/TestBidi.java#L397
 */

$bidi = new \IntlBidi();

$bidi->setPara("abc          ", \IntlBidi::RTL);
$bidiLine = $bidi->setLine(0, 6);
for ($i = 3; $i < 6; $i++) {
    var_dump($bidiLine->getLevelAt($i) === \IntlBidi::RTL);
}

$bidi->setPara("abc       def", \IntlBidi::RTL, null);
$bidiLine = $bidi->setLine(0, 6);
for ($i = 3; $i < 6; $i++) {
    var_dump($bidiLine->getLevelAt($i) === \IntlBidi::RTL);
}

$bidi->setPara("abcdefghi    ", \IntlBidi::RTL, null);
$bidiLine = $bidi->setLine(0, 6);
for ($i = 3; $i < 6; $i++) {
    var_dump($bidiLine->getLevelAt($i) === \IntlBidi::MIXED);
}

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
==DONE==