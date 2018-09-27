--TEST--
U_BUFFER_OVERFLOW_ERROR error. (IS EMBEDDED IN OTHER TEST, BUT EXTRACTED AND REDUCED FOR SIMPLICITY)
--CREDITS--
Timo Scholz <timo.scholz@setasign.com>
--SKIPIF--
<?php if ( !extension_loaded('intl')) print 'skip'; ?>
--FILE--
<?php
$bidi = new \IntlBidi();
$bidi->setReorderingMode(\IntlBidi::REORDER_RUNS_ONLY);
$bidi->setReorderingOptions(\IntlBidi::OPTION_INSERT_MARKS);

$bidi->setPara('ab 234' . "\x20\xD9\xA8\xD9\xA9\xD9\xA6" . 'de', 0);
var_dump(bin2hex($bidi->getReordered(\IntlBidi::DO_MIRRORING))); // U_BUFFER_OVERFLOW_ERROR
?>
==DONE==
--EXPECT--
string(42) "61622032333420e2808ed9a8d9a9d9a6e2808e6465"
==DONE==