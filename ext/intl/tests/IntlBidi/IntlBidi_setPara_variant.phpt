--TEST--
Check exceeding para level
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
 * Original: https://github.com/friedrich/icu/blob/master/icu4j/main/tests/core/src/com/ibm/icu/dev/test/bidi/TestBidi.java#L520
 */

$bidi = new \IntlBidi();
$bidi->setPara("A\u{202a}\u{05d0}\u{202a}C\u{202c}\u{05d1}\u{202c}E", IntlBidi::MAX_EXPLICIT_LEVEL - 1);
var_dump($bidi->getLevelAt(2) === IntlBidi::MAX_EXPLICIT_LEVEL);
?>
==DONE==
--EXPECT--
bool(true)
==DONE==
