--TEST--
Conditional Assignment Static Props
--FILE--
<?php

class A {
  public static $w = false;
  public static $x = 123;
  protected static $y = 234;
  private static $z = 345;
}

A::$w ?:= 456;
var_dump(A::$w);

A::$x ?:= 567;
var_dump(A::$x);

try {
  A::$y ?:= 678;
  var_dump(A::$y);
} catch (\Throwable $e) {
  echo "Exception: ", $e->getMessage(), "\n";
}

try {
  A::$z ?:= 789;
  var_dump(A::$z);
} catch (\Throwable $e) {
  echo "Exception: ", $e->getMessage(), "\n";
}

--EXPECTF--
int(456)
int(123)
Exception: Cannot access protected property A::$y
Exception: Cannot access private property A::$z
