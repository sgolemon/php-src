--TEST--
Declare Vars in a function scope (positive)
--FILE--
<?php
declare(declare_vars=1);

function test() {
  var $foo; // Implicit NULL value.
  var_dump($foo);

  var $bar = 1;
  var_dump($bar);
}

test();
echo "Done";
--EXPECTF--
NULL
int(1)
Done
