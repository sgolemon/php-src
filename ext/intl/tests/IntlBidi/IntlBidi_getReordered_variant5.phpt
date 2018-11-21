--TEST--
Test getReordered() with previous setReorderingOptions(\IntlBidi::OPTION_INSERT_MARKS) + \IntlBidi::INSERT_LRM_FOR_NUMERIC in $options argument.
--CREDITS--
Timo Scholz <timo.scholz@setasign.com>
--SKIPIF--
<?php if ( !extension_loaded('intl')) print 'skip'; ?>
--FILE--
<?php
$bidi = new \IntlBidi();
$bidi->setReorderingMode(\IntlBidi::REORDER_RUNS_ONLY);
$bidi->setReorderingOptions(\IntlBidi::OPTION_INSERT_MARKS);

$bidi->setPara(hex2bin('61622032333420e2808ed9a8d9a9d9a6e2808e6465'), 0);
$bidi->setInverse(true);
$a = $bidi->getReordered(\IntlBidi::DO_MIRRORING);
$b = $bidi->getReordered(\IntlBidi::DO_MIRRORING | \IntlBidi::INSERT_LRM_FOR_NUMERIC);
// because \IntlBidi::OPTION_INSERT_MARKS isset we've no difference
var_dump($a === $b);
var_dump(bin2hex($a));

// reset the global reordering options
$bidi->setReorderingOptions(\IntlBidi::OPTION_DEFAULT);
$a = $bidi->getReordered(\IntlBidi::DO_MIRRORING);
$b = $bidi->getReordered(\IntlBidi::DO_MIRRORING | \IntlBidi::INSERT_LRM_FOR_NUMERIC);
var_dump($a === $b); // false
var_dump(bin2hex($a));
var_dump(bin2hex($b));
?>
==DONE==
--EXPECT--
bool(true)
string(48) "61622032333420e2808ee2808ed9a8d9a9d9a6e2808e6465"
bool(false)
string(30) "61622032333420d9a8d9a9d9a66465"
string(48) "61622032333420e2808ee2808ed9a8d9a9d9a6e2808e6465"
==DONE==