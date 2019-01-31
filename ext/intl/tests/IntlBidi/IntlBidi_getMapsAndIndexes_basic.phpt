--TEST--
Test for IntlBidi::getVisualIndex(), IntlBidi::getLogicalIndex(), IntlBidi::getLogicMap(), IntlBidi::getVisualMap()
--CREDITS--
Jan Slabon <jan.slabon@setasign.com>
--SKIPIF--
<?php if (!extension_loaded('intl')) print 'skip'; ?>
<?php if (!extension_loaded('mbstring')) print 'skip'; ?>
--FILE--
<?php
// include helper functions
include 'IntlBidi_ut_common.inc';

$bidi = new \IntlBidi();

$string = pseudoToU8('(KC)add(K.C.&)');
$chars = preg_split('/(?<!^)(?!$)/u', $string);

$bidi->setPara($string);

$logicalMap = $bidi->getLogicalMap();
$visualMap = $bidi->getVisualMap();

var_dump(count($logicalMap));
var_dump($bidi->getProcessedLength() === count($logicalMap));

// We do cross checks in both tables and getter methods:
foreach ($chars as $logicalIndex => $char) {
    $visualIndex = $bidi->getVisualIndex($logicalIndex);
    var_dump($visualIndex === $logicalMap[$visualMap[$visualIndex]]);
    var_dump($visualIndex === $logicalMap[$logicalIndex]);
    var_dump($logicalIndex === $bidi->getLogicalIndex($visualIndex));
}

// That's how this is done manually/reproducable:

//var_dump(u8ToPseudo($bidi->getReordered(0)));

//$logicalIndex = 0; // (
//$visualIndex = $bidi->getVisualIndex($logicalIndex);
//var_dump($visualIndex === 13, $visualIndex === $logicalMap[$logicalIndex]);
//var_dump($visualIndex === $logicalMap[$logicalIndex]);
//var_dump($bidi->getLogicalIndex($visualIndex) === $logicalIndex);
//
//$logicalIndex = 1; // K
//$visualIndex = $bidi->getVisualIndex($logicalIndex);
//var_dump($visualIndex === 12, $visualIndex === $logicalMap[$visualMap[$visualIndex]]);
//var_dump($visualIndex === $logicalMap[$logicalIndex]);
//var_dump($bidi->getLogicalIndex($visualIndex) === $logicalIndex);
//
//$logicalIndex = 2; // C
//$visualIndex = $bidi->getVisualIndex($logicalIndex);
//var_dump($visualIndex === 11, $visualIndex === $logicalMap[$visualMap[$visualIndex]]);
//var_dump($visualIndex === $logicalMap[$logicalIndex]);
//var_dump($bidi->getLogicalIndex($visualIndex) === $logicalIndex);
//
//$logicalIndex = 3; // )
//$visualIndex = $bidi->getVisualIndex($logicalIndex);
//var_dump($visualIndex === 10, $visualIndex === $logicalMap[$visualMap[$visualIndex]]);
//var_dump($bidi->getLogicalIndex($visualIndex) === $logicalIndex);
//
//$logicalIndex = 4; // a
//$visualIndex = $bidi->getVisualIndex($logicalIndex);
//var_dump($visualIndex === 7, $visualIndex === $logicalMap[$visualMap[$visualIndex]]);
//var_dump($bidi->getLogicalIndex($visualIndex) === $logicalIndex);
//
//$logicalIndex = 5; // d
//$visualIndex = $bidi->getVisualIndex($logicalIndex);
//var_dump($visualIndex === 8, $visualIndex === $logicalMap[$visualMap[$visualIndex]]);
//var_dump($bidi->getLogicalIndex($visualIndex) === $logicalIndex);
//
//$logicalIndex = 6; // d
//$visualIndex = $bidi->getVisualIndex($logicalIndex);
//var_dump($visualIndex === 9, $visualIndex === $logicalMap[$visualMap[$visualIndex]]);
//var_dump($bidi->getLogicalIndex($visualIndex) === $logicalIndex);

// ...

//var_dump($logicalMap);
//var_dump($visualMap);

?>
==DONE==
--EXPECT--
int(14)
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
