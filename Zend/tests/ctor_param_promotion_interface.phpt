--TEST--
Constructor Param Promotion (Interface)
--FILE--
<?php

interface A {
  public function __construct(public $f);
}
--EXPECTF--
Fatal error: Constructor parameter promotion not allowed on traits or interfaces in %s on line 4
