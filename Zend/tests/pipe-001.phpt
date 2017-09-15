--TEST--
PipeOp Basic Usage
--FILE--
<?php

class C {
  public $val;
  public function __construct($val) { $this->val = $val; }
  public function add($x) { return $x + $this->val; }
  static public function halve($x) { return $x / 2; }
}

var_dump("Hello World" |> 'strtoupper');
var_dump(123 |> function($x){ return $x * 2; });
var_dump(42 |> [new C(5),'add']);
var_dump(12 |> ['C', 'halve']);
--EXPECT--
string(11) "HELLO WORLD"
int(246)
int(47)
int(6)
