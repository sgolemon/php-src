--TEST--
Constructor Param Promotion (Trait)
--FILE--
<?php

trait A {
  public function __construct(public $f) {}
  public function foo() {}
}

--EXPECTF--
Fatal error: Constructor parameter promotion not allowed on traits or interfaces in %s on line 4
