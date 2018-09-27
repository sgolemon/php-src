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
 * Basic functions for the Bidi test suite.
 * Original: https://github.com/unicode-org/icu/blob/master/icu4j/main/tests/core/src/com/ibm/icu/dev/test/bidi/BidiFmwk.java
 */

// --- INIT VISUAL MAPPING ---

function initMapping()
{
    $pseudoToUChar = [];
    $uCharToPseudo = [];
    $uCharToPseudo2 = [];

// map real chars to different unicode values.
    $pseudoToUChar[0] = 0x0000;
    $uCharToPseudo[0x00] = 0;
    $pseudoToUChar[' '] = 0x0020;
    $uCharToPseudo[0x20] = ' ';
    $pseudoToUChar['!'] = 0x0021;
    $uCharToPseudo[0x21] = '!';
    $pseudoToUChar['"'] = 0x0022;
    $uCharToPseudo[0x22] = '"';
    $pseudoToUChar['#'] = 0x0023;
    $uCharToPseudo[0x23] = '#';
    $pseudoToUChar['$'] = 0x0024;
    $uCharToPseudo[0x24] = '$';
    $pseudoToUChar['%'] = 0x0025;
    $uCharToPseudo[0x25] = '%';
    $pseudoToUChar['\''] = 0x0027;
    $uCharToPseudo[0x27] = '\'';
    $pseudoToUChar['('] = 0x0028;
    $uCharToPseudo[0x28] = '(';
    $pseudoToUChar[')'] = 0x0029;
    $uCharToPseudo[0x29] = ')';
    $pseudoToUChar['*'] = 0x002A;
    $uCharToPseudo[0x2A] = '*';
    $pseudoToUChar['+'] = 0x002B;
    $uCharToPseudo[0x2B] = '+';
    $pseudoToUChar[','] = 0x002C;
    $uCharToPseudo[0x2C] = ',';
    $pseudoToUChar['-'] = 0x002D;
    $uCharToPseudo[0x2D] = '-';
    $pseudoToUChar['.'] = 0x002E;
    $uCharToPseudo[0x2E] = '.';
    $pseudoToUChar['/'] = 0x002F;
    $uCharToPseudo[0x2F] = '/';
    $pseudoToUChar[':'] = 0x003A;
    $uCharToPseudo[0x3A] = ':';
    $pseudoToUChar[';'] = 0x003B;
    $uCharToPseudo[0x3B] = ';';
    $pseudoToUChar['<'] = 0x003C;
    $uCharToPseudo[0x3C] = '<';
    $pseudoToUChar['='] = 0x003D;
    $uCharToPseudo[0x3D] = '=';
    $pseudoToUChar['>'] = 0x003E;
    $uCharToPseudo[0x3E] = '>';
    $pseudoToUChar['?'] = 0x003F;
    $uCharToPseudo[0x3F] = '?';
    $pseudoToUChar['\\'] = 0x005C;
    $uCharToPseudo[0x5C] = '\\';

    /* initialize specially used characters */
    $pseudoToUChar['`'] = 0x3000;
    $uCharToPseudo2[0x00] = '`';  /* NSM */
    $pseudoToUChar['@'] = 0x200E;
    $uCharToPseudo2[0x0E] = '@';  /* LRM */
    $pseudoToUChar['&'] = 0x200F;
    $uCharToPseudo2[0x0F] = '&';  /* RLM */
    $pseudoToUChar['_'] = 0x001F;
    $uCharToPseudo[0x1F] = '_';  /* S   */
    $pseudoToUChar['|'] = 0x2029;
    $uCharToPseudo2[0x29] = '|';  /* B   */
    $pseudoToUChar['['] = 0x202A;
    $uCharToPseudo2[0x2A] = '[';  /* LRE */
    $pseudoToUChar[']'] = 0x202B;
    $uCharToPseudo2[0x2B] = ']';  /* RLE */
    $pseudoToUChar['^'] = 0x202C;
    $uCharToPseudo2[0x2C] = '^';  /* PDF */
    $pseudoToUChar['{'] = 0x202D;
    $uCharToPseudo2[0x2D] = '{';  /* LRO */
    $pseudoToUChar['}'] = 0x202E;
    $uCharToPseudo2[0x2E] = '}';  /* RLO */
    $pseudoToUChar['~'] = 0x007F;
    $uCharToPseudo[0x7F] = '~'; /* BN  */

    $columns_str = str_split('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz');
    $columns = [];
    foreach ($columns_str as $value) {
        $columns[] = \IntlChar::chr(\ord($value));
    }

    /* initialize western digits */
    for ($i = 0, $uchar = 0x0030; $i < 6; $i++, $uchar++) {
        $c = $columns[$i];
        $pseudoToUChar[$c] = $uchar;
        $uCharToPseudo[$uchar & 0x00FF] = $c;
    }
    /* initialize Hindi digits */
    for ($i = 6, $uchar = 0x0666; $i < 10; $i++, $uchar++) {
        $c = $columns[$i];
        $pseudoToUChar[$c] = $uchar;
        $uCharToPseudo2[$uchar & 0x00FF] = $c;
    }
    /* initialize Arabic letters */
    for ($i = 10, $uchar = 0x0631; $i < 16; $i++, $uchar++) {
        $c = $columns[$i];
        $pseudoToUChar[$c] = $uchar;
        $uCharToPseudo2[$uchar & 0x00FF] = $c;
    }
    /* initialize Hebrew letters */
    for ($i = 16, $uchar = 0x05D7; $i < 32; $i++, $uchar++) {
        $c = $columns[$i];
        $pseudoToUChar[$c] = $uchar;
        $uCharToPseudo2[$uchar & 0x00FF] = $c;
    }
    /* initialize Unassigned code points */
    for ($i = 32, $uchar = 0x08D0; $i < 36; $i++, $uchar++) {
        $c = $columns[$i];
        $pseudoToUChar[$c] = $uchar;
        $uCharToPseudo2[$uchar & 0x00FF] = $c;
    }
    /* initialize Latin lower case letters */
    for ($i = 36, $uchar = 0x0061; $i < 62; $i++, $uchar++) {
        $c = $columns[$i];
        $pseudoToUChar[$c] = $uchar;
        $uCharToPseudo[$uchar & 0x00FF] = $c;
    }

    return [
        'pseudoToUChar' => $pseudoToUChar,
        'uCharToPseudo' => $uCharToPseudo,
        'uCharToPseudo2' => $uCharToPseudo2
    ];
}

function getMapping() {
    static $mapping = null;
    if ($mapping === null) {
        $mapping = initMapping();
    }

    return $mapping;
}

function pseudoToU8(string $input)
{
    $len = strlen($input);
    $output = '';

    for ($i = 0; $i < $len; $i++) {
        $output .= \IntlChar::chr(getMapping()['pseudoToUChar'][$input[$i]]);
    }

    return $output;
}

function u8ToPseudo(string $input)
{
    $encoding = 'utf-8';
    $len = \mb_strlen($input, $encoding);
    $output = '';

    $mapping = getMapping();

    for ($i = 0; $i < $len; $i++) {
        $uChar = \IntlChar::ord(\mb_substr($input, $i, 1, $encoding));
        $output .= $uChar < 0x0100 ? $mapping['uCharToPseudo'][$uChar] : $mapping['uCharToPseudo2'][$uChar & 0x00FF];
    }

    return $output;
}