--TEST--
Conditional Assignment Ref
--FILE--
<?php

$x = 5;
$y =& $x;
$x ?:= 6;
var_dump($x, $y);

unset($x, $y);

$y = 1;
$x = array('xx' => &$y, 'yy' => 2);
$x['xx'] ?:= 3;
$x['zz'] ?:= 4;
var_dump($x);
$y = 5;
var_dump($x);

--EXPECTF--
int(5)
int(5)

Notice: Undefined index: zz in %s/conditional_assign_007.php on line 13
array(3) {
  ["xx"]=>
  int(1)
  ["yy"]=>
  int(2)
  ["zz"]=>
  int(4)
}
array(3) {
  ["xx"]=>
  int(5)
  ["yy"]=>
  int(2)
  ["zz"]=>
  int(4)
}
