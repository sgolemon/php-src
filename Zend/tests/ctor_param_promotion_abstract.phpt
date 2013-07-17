--TEST--
Constructor Param Promotion (Abstract)
--FILE--
<?php

class A {
  public abstract function __construct(public $f);
}
--EXPECTF--
Fatal error: Parameter modifiers not allowed on abstract constructor in %s on line 4
