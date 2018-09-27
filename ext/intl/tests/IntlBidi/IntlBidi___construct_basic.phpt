--TEST--
Regression test for the UBA implementation.
--CREDITS--
Timo Scholz <timo.scholz@setasign.com>
--SKIPIF--
<?php if ( !extension_loaded('intl') ) print 'skip'; ?>
<?php if ( !extension_loaded('mbstring')) print 'skip'; ?>
--FILE--
<?php
$bidi = new \IntlBidi();
?>
==DONE==
--EXPECT--
==DONE==