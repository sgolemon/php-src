--TEST--
Conditional Assignment Side Effects
--FILE--
<?php

$x = array(0 => "allocated buffer");
$x[1] ?:= ($x = null);
var_dump($x);

--EXPECTF--
Notice: Undefined offset: 1 in %s/conditional_assign_009.php on line 4
array(1) {
  [1]=>
  NULL
}
