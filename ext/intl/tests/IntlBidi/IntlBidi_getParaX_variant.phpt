--TEST--
Test IntlBidi::getPara*() methods: getParaLevel(), getParagraph(), getParagraphByIndex()
--CREDITS--
Jan Slabon <jan.slabon@setasign.com>
--SKIPIF--
<?php if (!extension_loaded('intl')) print 'skip'; ?>
--FILE--
<?php
$bidi = new \IntlBidi();

$string = 'a simple test';
$bidi->setPara($string);
var_dump($bidi->getParagraph(0));
var_dump($bidi->getParaLevel() === \IntlBidi::LTR);
echo "\n";

$bidi->setPara($string, \IntlBidi::RTL);
var_dump($bidi->getParagraph(5));
var_dump($bidi->getParaLevel() === \IntlBidi::RTL);
echo "\n";

$bidi->setPara("ABC\r\nDEF");
var_dump($bidi->getParagraph(4)); // position of the block separator
var_dump($bidi->getParagraphByIndex(0));
echo "\n";

var_dump($bidi->getParagraph(5)); // position after the block separator
var_dump($bidi->getParagraphByIndex(1));


echo "\n";
?>
==DONE==
--EXPECT--
array(4) {
  ["index"]=>
  int(0)
  ["start"]=>
  int(0)
  ["limit"]=>
  int(13)
  ["level"]=>
  int(0)
}
bool(true)

array(4) {
  ["index"]=>
  int(0)
  ["start"]=>
  int(0)
  ["limit"]=>
  int(13)
  ["level"]=>
  int(1)
}
bool(true)

array(4) {
  ["index"]=>
  int(0)
  ["start"]=>
  int(0)
  ["limit"]=>
  int(5)
  ["level"]=>
  int(0)
}
array(4) {
  ["index"]=>
  int(0)
  ["start"]=>
  int(0)
  ["limit"]=>
  int(5)
  ["level"]=>
  int(0)
}

array(4) {
  ["index"]=>
  int(1)
  ["start"]=>
  int(5)
  ["limit"]=>
  int(8)
  ["level"]=>
  int(0)
}
array(4) {
  ["index"]=>
  int(1)
  ["start"]=>
  int(5)
  ["limit"]=>
  int(8)
  ["level"]=>
  int(0)
}

==DONE==