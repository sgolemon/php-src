--TEST--
Test for IntlBidi countParagraphs
This currently fails, i still need to check if the test has the wrong implementation or if Bidi is giving faulty results.
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
 * Original: https://github.com/unicode-org/icu/blob/778d0a6d1d46faa724ead19613bda84621794b72/icu4j/main/tests/core/src/com/ibm/icu/dev/test/bidi/TestBidi.java
 */
// https://github.com/unicode-org/icu/blob/778d0a6d1d46faa724ead19613bda84621794b72/icu4j/main/tests/core/src/com/ibm/icu/dev/test/bidi/TestData.java#L23
$L = \IntlChar::CHAR_DIRECTION_LEFT_TO_RIGHT;
$R = \IntlChar::CHAR_DIRECTION_RIGHT_TO_LEFT;
$EN = \IntlChar::CHAR_DIRECTION_EUROPEAN_NUMBER;
$ES = \IntlChar::CHAR_DIRECTION_EUROPEAN_NUMBER_SEPARATOR;
$ET = \IntlChar::CHAR_DIRECTION_EUROPEAN_NUMBER_TERMINATOR;
$AN = \IntlChar::CHAR_DIRECTION_ARABIC_NUMBER;
$CS = \IntlChar::CHAR_DIRECTION_COMMON_NUMBER_SEPARATOR;
$B = \IntlChar::CHAR_DIRECTION_BLOCK_SEPARATOR;
$S = \IntlChar::CHAR_DIRECTION_SEGMENT_SEPARATOR;
$WS = \IntlChar::CHAR_DIRECTION_WHITE_SPACE_NEUTRAL;
$ON = \IntlChar::CHAR_DIRECTION_OTHER_NEUTRAL;
$LRE = \IntlChar::CHAR_DIRECTION_LEFT_TO_RIGHT_EMBEDDING;
$LRO = \IntlChar::CHAR_DIRECTION_LEFT_TO_RIGHT_OVERRIDE;
$AL = \IntlChar::CHAR_DIRECTION_RIGHT_TO_LEFT_ARABIC;
$RLE = \IntlChar::CHAR_DIRECTION_RIGHT_TO_LEFT_EMBEDDING;
$RLO = \IntlChar::CHAR_DIRECTION_RIGHT_TO_LEFT_OVERRIDE;
$PDF = \IntlChar::CHAR_DIRECTION_POP_DIRECTIONAL_FORMAT;
$NSM = \IntlChar::CHAR_DIRECTION_DIR_NON_SPACING_MARK;
$BN = \IntlChar::CHAR_DIRECTION_BOUNDARY_NEUTRAL;
$FSI = \IntlChar::CHAR_DIRECTION_FIRST_STRONG_ISOLATE;
$LRI = \IntlChar::CHAR_DIRECTION_LEFT_TO_RIGHT_ISOLATE;
$RLI = \IntlChar::CHAR_DIRECTION_RIGHT_TO_LEFT_ISOLATE;
$PDI = \IntlChar::CHAR_DIRECTION_POP_DIRECTIONAL_ISOLATE;

$DEF = \IntlChar::CHAR_DIRECTION_CHAR_DIRECTION_COUNT; // why ever this is 19 in Java

// https://github.com/unicode-org/icu/blob/778d0a6d1d46faa724ead19613bda84621794b72/icu4j/main/tests/core/src/com/ibm/icu/dev/test/bidi/TestData.java#L53
$testDirProps = [
    [$L, $L, $WS, $L, $WS, $EN, $L, $B],
    [$R, $AL, $WS, $R, $AL, $WS, $R],
    [$L, $L, $WS, $EN, $CS, $WS, $EN, $CS, $EN, $WS, $L, $L],
    [$L, $AL, $AL, $AL, $L, $AL, $AL, $L, $WS, $EN, $CS, $WS, $EN, $CS, $EN, $WS, $L, $L],
    [$AL, $R, $AL, $WS, $EN, $CS, $WS, $EN, $CS, $EN, $WS, $R, $R, $WS, $L, $L],
    [$R, $EN, $NSM, $ET],
    [$RLE, $WS, $R, $R, $R, $WS, $PDF, $WS, $B],
    [
        $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE,
        $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE,
        $AN, $RLO, $NSM, $LRE, $PDF, $RLE, $ES, $EN, $ON
    ],
    [
        $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE,
        $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE, $LRE,
        $LRE, $BN, $CS, $RLO, $S, $PDF, $EN, $LRO, $AN, $ES
    ],
    [
        $S, $WS, $NSM, $RLE, $WS, $L, $L, $L, $WS, $LRO, $WS, $R, $R, $R, $WS, $RLO, $WS, $L, $L,
        $L, $WS, $LRE, $WS, $R, $R, $R, $WS, $PDF, $WS, $L, $L, $L, $WS, $PDF, $WS, $AL, $AL,
        $AL, $WS, $PDF, $WS, $L, $L, $L, $WS, $PDF, $WS, $L, $L, $L, $WS, $PDF, $ON, $PDF,
        $BN, $BN, $ON, $PDF
    ],
    [
        $NSM, $WS, $L, $L, $L, $L, $L, $L, $L, $WS, $L, $L, $L, $L, $WS, $R, $R, $R, $R, $R, $WS,
        $L, $L, $L, $L, $L, $L, $L, $WS, $WS, $AL, $AL, $AL, $AL, $WS, $EN, $EN, $ES, $EN,
        $EN, $CS, $S, $EN, $EN, $CS, $WS, $EN, $EN, $WS, $AL, $AL, $AL, $AL, $AL, $B, $L, $L,
        $L, $L, $L, $L, $L, $L, $WS, $AN, $AN, $CS, $AN, $AN, $WS
    ],
    [
        $NSM, $WS, $L, $L, $L, $L, $L, $L, $L, $WS, $L, $L, $L, $L, $WS, $R, $R, $R, $R, $R, $WS,
        $L, $L, $L, $L, $L, $L, $L, $WS, $WS, $AL, $AL, $AL, $AL, $WS, $EN, $EN, $ES, $EN,
        $EN, $CS, $S, $EN, $EN, $CS, $WS, $EN, $EN, $WS, $AL, $AL, $AL, $AL, $AL, $B, $L, $L,
        $L, $L, $L, $L, $L, $L, $WS, $AN, $AN, $CS, $AN, $AN, $WS
    ],
    [
        $NSM, $WS, $L, $L, $L, $L, $L, $L, $L, $WS, $L, $L, $L, $L, $WS, $R, $R, $R, $R, $R, $WS,
        $L, $L, $L, $L, $L, $L, $L, $WS, $WS, $AL, $AL, $AL, $AL, $WS, $EN, $EN, $ES, $EN,
        $EN, $CS, $S, $EN, $EN, $CS, $WS, $EN, $EN, $WS, $AL, $AL, $AL, $AL, $AL, $B, $L, $L,
        $L, $L, $L, $L, $L, $L, $WS, $AN, $AN, $CS, $AN, $AN, $WS
    ],
    [
        $NSM, $WS, $L, $L, $L, $L, $L, $L, $L, $WS, $L, $L, $L, $L, $WS, $R, $R, $R, $R, $R, $WS,
        $L, $L, $L, $L, $L, $L, $L, $WS, $WS, $AL, $AL, $AL, $AL, $WS, $EN, $EN, $ES, $EN,
        $EN, $CS, $S, $EN, $EN, $CS, $WS, $EN, $EN, $WS, $AL, $AL, $AL, $AL, $AL, $B, $L, $L,
        $L, $L, $L, $L, $L, $L, $WS, $AN, $AN, $CS, $AN, $AN, $WS
    ],
    [
        $NSM, $WS, $L, $L, $L, $L, $L, $L, $L, $WS, $L, $L, $L, $L, $WS, $R, $R, $R, $R, $R, $WS,
        $L, $L, $L, $L, $L, $L, $L, $WS, $WS, $AL, $AL, $AL, $AL, $WS, $EN, $EN, $ES, $EN,
        $EN, $CS, $S, $EN, $EN, $CS, $WS, $EN, $EN, $WS, $AL, $AL, $AL, $AL, $AL, $B, $L, $L,
        $L, $L, $L, $L, $L, $L, $WS, $AN, $AN, $CS, $AN, $AN, $WS
    ],
    [$ON, $L, $RLO, $CS, $R, $WS, $AN, $AN, $PDF, $LRE, $R, $L, $LRO, $WS, $BN, $ON, $S, $LRE, $LRO, $B],
    [$ON, $L, $RLO, $CS, $R, $WS, $AN, $AN, $PDF, $LRE, $R, $L, $LRO, $WS, $BN, $ON, $S, $LRE, $LRO, $B],
    [$RLO, $RLO, $AL, $AL, $WS, $EN, $ES, $ON, $WS, $S, $S, $PDF, $LRO, $WS, $AL, $ET, $RLE, $ON, $EN, $B],
    [$R, $L, $CS, $L],
    [$L, $L, $L, $WS, $L, $L, $L, $WS, $L, $L, $L],
    [$R, $R, $R, $WS, $R, $R, $R, $WS, $R, $R, $R],
    [$L],
];

$paraLevels = [
    IntlBidi::DEFAULT_LTR, IntlBidi::DEFAULT_LTR, IntlBidi::DEFAULT_LTR,
    IntlBidi::DEFAULT_LTR, IntlBidi::DEFAULT_LTR, IntlBidi::DEFAULT_LTR,
    IntlBidi::DEFAULT_LTR, 64,                    64,
    IntlBidi::DEFAULT_LTR, IntlBidi::DEFAULT_LTR, IntlBidi::DEFAULT_RTL,
    2, 5, IntlBidi::DEFAULT_LTR, IntlBidi::DEFAULT_LTR, IntlBidi::DEFAULT_LTR,
    IntlBidi::DEFAULT_LTR, IntlBidi::DEFAULT_LTR, IntlBidi::RTL, IntlBidi::LTR, IntlBidi::RTL,
    IntlBidi::DEFAULT_LTR
];

$testDirections = [
    \IntlBidi::LTR, \IntlBidi::RTL, \IntlBidi::LTR, \IntlBidi::MIXED, \IntlBidi::MIXED, \IntlBidi::MIXED,
    \IntlBidi::RTL, \IntlBidi::MIXED, \IntlBidi::MIXED, \IntlBidi::MIXED, \IntlBidi::MIXED, \IntlBidi::MIXED,
    \IntlBidi::MIXED, \IntlBidi::MIXED, \IntlBidi::MIXED, \IntlBidi::MIXED, \IntlBidi::MIXED, \IntlBidi::RTL,
    \IntlBidi::LTR, \IntlBidi::MIXED, \IntlBidi::MIXED, \IntlBidi::MIXED, \IntlBidi::LTR
];

$lineStarts = [
    -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 0, 13,
    2, 0, 0, -1, -1
];

$lineLimits = [
    -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 6, 14,
    3, 8, 8, -1, -1
];

function getStringFromDirProps($dirProp)
{
    $charFromDirProp = [
        /* L      R    EN    ES    ET     AN    CS    B    S    WS    ON */
        0x61, 0x5d0, 0x30, 0x2f, 0x25, 0x660, 0x2c, 0xa, 0x9, 0x20, 0x26,
        /* LRE     LRO     AL     RLE     RLO     PDF    NSM      BN */
        0x202a, 0x202d, 0x627, 0x202b, 0x202e, 0x202c, 0x308, 0x200c,
        /* FSI     LRI     RLI     PDI */
        0x2068, 0x2066, 0x2067, 0x2069  /* new in Unicode 6.3/ICU 52 */
    ];

    $buffer = '';
    foreach ($dirProp as $value) {
        $buffer .= \IntlChar::chr($charFromDirProp[$value]);
    }

    return $buffer;
}

for ($index = 0, $indexMax = count($testDirProps); $index < $indexMax; $index++) {
    $bidi = new \IntlBidi();
    $bidi->setPara(getStringFromDirProps($testDirProps[$index]), $paraLevels[$index]);
    $lineStart = $lineStarts[$index];
    if ($lineStart === -1) {
        $line = $bidi;
    } else {
        $line = $bidi->setLine($lineStart, $lineLimits[$index]);
    }

    var_dump($line->getDirection() === $testDirections[$index]);
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
