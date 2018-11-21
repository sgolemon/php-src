--TEST--
Test for IntlBidi countParagraphs
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
 * Original: https://github.com/unicode-org/icu/blob/778d0a6d1d46faa724ead19613bda84621794b72/icu4j/main/tests/core/src/com/ibm/icu/dev/test/bidi/TestMultipleParagraphs.java#L28
 */

$bidi = new \IntlBidi();

$bidi->setPara('', IntlBidi::LTR, null);
var_dump($bidi->countParagraphs());

$text = "__ABC\u{001c}"                  /* Para #0 offset 0 */
        . "__\u{05d0}DE\u{001c}"           /*       1        6 */
        . "__123\u{001c}"                /*       2       12 */
        . "\r\n"                       /*       3       18 */
        . "FG\r"                       /*       4       20 */
        . "\r"                         /*       5       23 */
        . "HI\r\n"                     /*       6       24 */
        . "\r\n"                       /*       7       28 */
        . "\n"                         /*       8       30 */
        . "\n"                         /*       9       31 */
        . "JK\u{001c}"; /*      10       32 */

$bidi->setPara($text, IntlBidi::LTR, null);

var_dump($bidi->countParagraphs());
?>
==DONE==
--EXPECT--
int(0)
int(11)
==DONE==