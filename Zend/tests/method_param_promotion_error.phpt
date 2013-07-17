--TEST--
Method Parameter Promotion (invalid use)
--FILE--
<?php

//
// only constructors can promote parameters
//
class A {
  public function f(protected $c) {}
}

--EXPECTF--
Fatal error: Parameter modifiers not allowed on methods in %s on line 7
