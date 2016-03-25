--TEST--
Conditional Assignment Indirect Object
--FILE--
<?php

$ao = new ArrayObject(['x' => 5], ArrayObject::ARRAY_AS_PROPS);
$ao->x ?:= 6;
$ao->y ?:= 7;
var_dump($ao->getArrayCopy());

--EXPECTF--
Notice: Undefined index: y in %s/conditional_assign_010.php on line 5
array(2) {
  ["x"]=>
  int(5)
  ["y"]=>
  int(7)
}
