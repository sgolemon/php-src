--TEST--
Basic usages of the nullsafe operator
--FILE--
<?php

class A {
  public $x;
  public $y;

  public function getSelf() {
    return $this;
  }

  public function getNothing() {
    return null;
  }
}

$a = null;
var_dump($a?->getSelf());

$a = new A;
var_dump($a?->getSelf());
var_dump($a?->getNothing());
var_dump($a?->getNothing()?->getSelf());
var_dump($a->getNothing()?->getSelf());

var_dump($a->x?->y);
var_dump($a->getNothing()?->x);
$a->x = new A;
var_dump($a->x?->getSelf());
var_dump($a->x?->getNothing()?->x);
var_dump($a->y?->getNothing()?->getNothing());

$b = 'c';
$c = null;
var_dump($$b?->getSelf());
$c = new A;
var_dump($$b?->getSelf());
--EXPECTF--
NULL
object(A)#%d (2) {
  ["x"]=>
  NULL
  ["y"]=>
  NULL
}
NULL
NULL
NULL
NULL
NULL
object(A)#%d (2) {
  ["x"]=>
  NULL
  ["y"]=>
  NULL
}
NULL
NULL
NULL
object(A)#%d (2) {
  ["x"]=>
  NULL
  ["y"]=>
  NULL
}
