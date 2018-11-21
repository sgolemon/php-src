--TEST--
Test IntlBidi::getLevels() method
--CREDITS--
Jan Slabon <jan.slabon@setasign.com>
--SKIPIF--
<?php if (!extension_loaded('intl')) print 'skip'; ?>
<?php if (!extension_loaded('mbstring')) print 'skip'; ?>
--FILE--
<?php

// include helper functions
include 'IntlBidi_ut_common.inc';

$original = 'del(KC)add(K.C.&)';
$string = pseudoToU8($original);

$bidi = new \IntlBidi();
$bidi->setPara($string);

$levels = $bidi->getLevels();

var_dump(strlen($levels) === strlen($original));

for ($i = 0, $len = strlen($levels); $i < $len; $i++) {
    echo $original[$i] . ': ' . bin2hex($levels[$i]) . "\n";
}

echo "\n";

// do the with level set to RTL
$bidi = new \IntlBidi();
$bidi->setPara($string, \IntlBidi::RTL);
$levels = $bidi->getLevels();

for ($i = 0, $len = strlen($levels); $i < $len; $i++) {
    echo $original[$i] . ': ' . bin2hex($levels[$i]) . "\n";
}

?>
==DONE==
--EXPECT--
bool(true)
d: 00
e: 00
l: 00
(: 00
K: 01
C: 01
): 00
a: 00
d: 00
d: 00
(: 00
K: 01
.: 01
C: 01
.: 01
&: 01
): 00

d: 02
e: 02
l: 02
(: 01
K: 01
C: 01
): 01
a: 02
d: 02
d: 02
(: 01
K: 01
.: 01
C: 01
.: 01
&: 01
): 01
==DONE==