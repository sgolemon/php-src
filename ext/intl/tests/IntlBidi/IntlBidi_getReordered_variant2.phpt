--TEST--
This tests raised an U_ZERO_ERROR error during implementation.
--CREDITS--
Timo Scholz <timo.scholz@setasign.com>
--SKIPIF--
<?php if ( !extension_loaded('intl')) print 'skip'; ?>
--FILE--
<?php
$bidi = new \IntlBidi();
$bidi->setPara('', \IntlBidi::DEFAULT_LTR);
var_dump($bidi->getReordered(\IntlBidi::INSERT_LRM_FOR_NUMERIC));
?>
==DONE==
--EXPECT--
string(0) ""
==DONE==