+--TEST--
We keep this file to track untested/implemented features:
    The method getCustomizedClass() will currently not work, because there's no setter method at all.
--SKIPIF--
<?php if (!extension_loaded('intl')) print 'skip'; ?>
--FILE--
<?php
?>
==DONE==
--EXPECT--
invalid
==DONE==