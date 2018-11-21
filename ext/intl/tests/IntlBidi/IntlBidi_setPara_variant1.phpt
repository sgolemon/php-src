--TEST--
Test the behavior of the $embeddingLevels argument
--CREDITS--
Jan Slabon <jan.slabon@setasign.com>
--SKIPIF--
<?php if (!extension_loaded('intl')) print 'skip'; ?>
<?php if (!extension_loaded('mbstring')) print 'skip'; ?>
--FILE--
<?php

// include helper functions
include 'IntlBidi_ut_common.inc';

$original = 'a KC add K.C.';
$string = pseudoToU8($original);

$embeddingLevels = str_repeat("\0", strlen($original));

$bidi = new \IntlBidi();
$bidi->setPara($string, 0, $embeddingLevels);
var_dump(u8ToPseudo($bidi->getReordered(0)));

// let's revert the levels:
$embeddingLevels[0] = chr(\IntlBidi::LEVEL_OVERRIDE | \IntlBidi::RTL); // a

$embeddingLevels[2] = chr(\IntlBidi::LEVEL_OVERRIDE | \IntlBidi::LTR); // K
$embeddingLevels[3] = chr(\IntlBidi::LEVEL_OVERRIDE | \IntlBidi::LTR); // C

$embeddingLevels[5] = chr(\IntlBidi::LEVEL_OVERRIDE | \IntlBidi::RTL); // a
$embeddingLevels[6] = chr(\IntlBidi::LEVEL_OVERRIDE | \IntlBidi::RTL); // d
$embeddingLevels[7] = chr(\IntlBidi::LEVEL_OVERRIDE | \IntlBidi::RTL); // d

$embeddingLevels[9] = chr(\IntlBidi::LEVEL_OVERRIDE | \IntlBidi::LTR); // K
$embeddingLevels[10] = chr(\IntlBidi::LEVEL_OVERRIDE | \IntlBidi::LTR); // .
$embeddingLevels[11] = chr(\IntlBidi::LEVEL_OVERRIDE | \IntlBidi::LTR); // C
$embeddingLevels[12] = chr(\IntlBidi::LEVEL_OVERRIDE | \IntlBidi::LTR); // .

$bidi->setPara($string, \IntlBidi::DEFAULT_LTR | \IntlBidi::LEVEL_OVERRIDE, $embeddingLevels);
var_dump(u8ToPseudo($bidi->getReordered(0)));
?>
==DONE==
--EXPECT--
string(13) "a CK add C.K."
string(13) "a KC dda K.C."
==DONE==
