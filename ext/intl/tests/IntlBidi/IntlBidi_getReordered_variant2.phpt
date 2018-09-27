--TEST--
U_ZERO_ERROR error. (IS EMBEDDED IN OTHER TEST, BUT EXTRACTED AND REDUCED FOR SIMPLICITY)
--CREDITS--
Timo Scholz <timo.scholz@setasign.com>
--SKIPIF--
<?php if ( !extension_loaded('intl')) print 'skip'; ?>
--FILE--
<?php
$bidi = new \IntlBidi();
$bidi->setPara('', \IntlBidi::DEFAULT_LTR);
// TODO: this function throws a U_ZERO_ERROR exception.
var_dump($bidi->getReordered(\IntlBidi::INSERT_LRM_FOR_NUMERIC));
?>
==DONE==
--EXPECT--
string(0) ""
==DONE==