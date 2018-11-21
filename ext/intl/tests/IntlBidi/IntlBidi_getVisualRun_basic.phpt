--TEST--
Test IntlBidi::getVisualRun() method.
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

var_dump($bidi->countRuns()); // 5

var_dump($bidi->getVisualRun(0)); // del(
var_dump($bidi->getVisualRun(1)); // KC
var_dump($bidi->getVisualRun(2)); // )add(
var_dump($bidi->getVisualRun(3)); // K.C.&
var_dump($bidi->getVisualRun(4)); // )

?>
==DONE==
--EXPECT--
int(5)
array(3) {
  ["start"]=>
  int(0)
  ["length"]=>
  int(4)
  ["direction"]=>
  int(0)
}
array(3) {
  ["start"]=>
  int(4)
  ["length"]=>
  int(2)
  ["direction"]=>
  int(1)
}
array(3) {
  ["start"]=>
  int(6)
  ["length"]=>
  int(5)
  ["direction"]=>
  int(0)
}
array(3) {
  ["start"]=>
  int(11)
  ["length"]=>
  int(5)
  ["direction"]=>
  int(1)
}
array(3) {
  ["start"]=>
  int(16)
  ["length"]=>
  int(1)
  ["direction"]=>
  int(0)
}
==DONE==