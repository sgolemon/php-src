--TEST--
Constructor Param Promotion (Bad Modifier)
--FILE--
<?php

//
// only public, private or protected allowed
//
class A {
  public function __construct(static $a) {}
}

--EXPECTF--
Fatal error: Invalid parameter modifiers on constructor in %s on line 7
