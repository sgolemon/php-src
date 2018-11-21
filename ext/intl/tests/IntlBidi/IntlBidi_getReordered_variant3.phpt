--TEST--
U_BUFFER_OVERFLOW_ERROR error.
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
 * Original: https://github.com/friedrich/icu/blob/master/icu4j/main/tests/core/src/com/ibm/icu/dev/test/bidi/TestBidi.java#L512
 */

$bidi = new \IntlBidi();
$bidi->setReorderingMode(\IntlBidi::REORDER_INVERSE_LIKE_DIRECT);
$bidi->setReorderingOptions(\IntlBidi::OPTION_INSERT_MARKS);
$bidi->orderParagraphsLTR(true);

$bidi->setPara("\u{05d0} 123 \t\u{05d1} 123 \u{05d2}", \IntlBidi::LTR);
var_dump($bidi->getReordered(0) === "\u{05d0} \u{200e}123\u{200e} \t\u{05d2} 123 \u{05d1}");
?>
==DONE==
--EXPECT--
bool(true)
==DONE==