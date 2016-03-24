--TEST--
Conditional Assignment Obj Basic
--FILE--
<?php

$x = (object)array('xx' => 5, 'yy' => 4);
$x->xx ?:= 6;
var_dump($x);

$x->zz ?:= 7;
var_dump($x);

$y->ww ?:= 8;
var_dump($y);

--EXPECTF--
object(stdClass)#1 (2) {
  ["xx"]=>
  int(5)
  ["yy"]=>
  int(4)
}

Notice: Undefined property: stdClass::$zz in %s/conditional_assign_003.php on line 7
object(stdClass)#1 (3) {
  ["xx"]=>
  int(5)
  ["yy"]=>
  int(4)
  ["zz"]=>
  int(7)
}

Notice: Undefined variable: y in %s/conditional_assign_003.php on line 10

Notice: Undefined property: stdClass::$ww in %s/conditional_assign_003.php on line 10
object(stdClass)#2 (1) {
  ["ww"]=>
  int(8)
}
