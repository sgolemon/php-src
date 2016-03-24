--TEST--
Conditional Assignment Dim
--FILE--
<?php

$x = array('xx' => 5, 'yy' => 4);
$x['xx'] ?:= 6;
var_dump($x);

$x['zz'] ?:= 7;
var_dump($x);

$y['ww'] ?:= 8;
var_dump($y);

--EXPECTF--
array(2) {
  ["xx"]=>
  int(5)
  ["yy"]=>
  int(4)
}

Notice: Undefined index: zz in %s/conditional_assign_002.php on line 7
array(3) {
  ["xx"]=>
  int(5)
  ["yy"]=>
  int(4)
  ["zz"]=>
  int(7)
}

Notice: Undefined variable: y in %s/conditional_assign_002.php on line 10

Notice: Undefined index: ww in %s/conditional_assign_002.php on line 10
array(1) {
  ["ww"]=>
  int(8)
}
