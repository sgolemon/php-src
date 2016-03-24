--TEST--
Conditional Assignment Obj Overloaded
--FILE--
<?php

class A {
  public $x = 123;
  protected $y = 234;
  private $z = 345;

  public function __get($prop) {
    return 42;
  }

  public function __set($prop, $val) { }

}

$a = new A;
$a->x ?:= 456;
$a->y ?:= 567;
$a->z ?:= 678;
$a->w ?:= 789;
var_dump($a);

--EXPECTF--
Notice: Indirect modification of overloaded property A::$y has no effect in %s/conditional_assign_004.php on line 18

Notice: Indirect modification of overloaded property A::$z has no effect in %s/conditional_assign_004.php on line 19

Notice: Indirect modification of overloaded property A::$w has no effect in %s/conditional_assign_004.php on line 20
object(A)#1 (3) {
  ["x"]=>
  int(123)
  ["y":protected]=>
  int(234)
  ["z":"A":private]=>
  int(345)
}
