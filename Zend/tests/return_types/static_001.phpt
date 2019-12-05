--TEST--
Basic static return type hint test
--FILE--
<?php

class A {
  static public function make(): static {
    return new static();
  }

  static public function bork(): static {
    return new A();
  }
}

class B extends A {}

var_dump(A::make());
var_dump(B::make());
var_dump(A::bork()); // okay by accident
try {
  var_dump(B::bork());
} catch (\TypeError $e) {
  var_dump($e->getMessage());
}
--EXPECTF--
object(A)#%d (0) {
}
object(B)#%d (0) {
}
object(A)#%d (0) {
}
string(74) "Return value of A::bork() must be an instance of B, instance of A returned"
