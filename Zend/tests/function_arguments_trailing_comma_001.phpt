--TEST--
Trailing commas in function argument declarations
--FILE--
<?php
function globalFunc($a,) {
  var_dump(__FUNCTION__, $a);
}
$closure = function($b,) {
  var_dump(__FUNCTION__, $b);
};
class bar {
  static public function baz($c,) {
    var_dump(__CLASS__, __METHOD__, $c);
  }
}

globalFunc(123);
$closure("456");
bar::baz(true);
--EXPECT--
string(10) "globalFunc"
int(123)
string(9) "{closure}"
string(3) "456"
string(3) "bar"
string(8) "bar::baz"
bool(true)
