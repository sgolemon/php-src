--TEST--
Check 1-char runs with RUNS_ONLY
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
 * Original: https://github.com/friedrich/icu/blob/master/icu4j/main/tests/core/src/com/ibm/icu/dev/test/bidi/TestBidi.java#L525
 */

$bidi = new \IntlBidi();
$bidi->setReorderingMode(IntlBidi::REORDER_RUNS_ONLY);
$bidi->setPara("a \u{05d0} b \u{05d1} c \u{05d2} d ", IntlBidi::LTR);
var_dump($bidi->countRuns());
?>
==DONE==
--EXPECT--
int(14)
==DONE==