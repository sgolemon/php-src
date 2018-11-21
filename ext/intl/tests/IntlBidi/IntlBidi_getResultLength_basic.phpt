--TEST--
Test get result length.
--CREDITS--
Timo Scholz <timo.scholz@setasign.com>
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
 * Original: https://github.com/unicode-org/icu/blob/778d0a6d1d46faa724ead19613bda84621794b72/icu4j/main/tests/core/src/com/ibm/icu/dev/test/bidi/TestBidi.java#L418
 */

$bidi = new IntlBidi(120, 66);

$str = "\u{200e}" . 'abc       def';
$bidi->setPara($str, IntlBidi::RTL);
$bidi->setReorderingOptions(IntlBidi::REMOVE_BIDI_CONTROLS);

var_dump($bidi->getResultLength());
$bidiLine2 = $bidi->setLine(0, 6);
var_dump($bidi->getResultLength());
var_dump($bidiLine2->getResultLength());



?>
==DONE==
--EXPECT--
int(14)
int(14)
int(5)
int(14)
==DONE==