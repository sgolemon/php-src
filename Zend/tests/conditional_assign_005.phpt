--TEST--
Conditional Assignment Obj Visibility
--FILE--
<?php

class A {
  public $x = 123;
  protected $y = 234;
  private $z = 345;
}

$a = new A;
$a->x ?:= 456;
try {
  $a->y ?:= 567;
} catch (\Throwable $e) {
  echo "Exception: ", $e->getMessage(), "\n";
}
try {
  $a->z ?:= 678;
} catch (\Throwable $e) {
  echo "Exception: ", $e->getMessage(), "\n";
}
$a->w ?:= 789;
var_dump($a);

--EXPECTF--
Exception: Cannot access protected property A::$y
Exception: Cannot access private property A::$z

Notice: Undefined property: A::$w in /home/sgolemon/dev/php-src/Zend/tests/conditional_assign_005.php on line 21
object(A)#1 (4) {
  ["x"]=>
  int(123)
  ["y":protected]=>
  int(234)
  ["z":"A":private]=>
  int(345)
  ["w"]=>
  int(789)
}

