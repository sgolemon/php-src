--TEST--
Basic test to check the construction of IntlBidi.
--CREDITS--
Timo Scholz <timo.scholz@setasign.com>
--SKIPIF--
<?php if (!extension_loaded('intl')) print 'skip'; ?>
--FILE--
<?php
$bidi = new \IntlBidi();
?>
==DONE==
--EXPECT--
==DONE==