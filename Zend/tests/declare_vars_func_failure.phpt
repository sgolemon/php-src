--TEST--
Declare Vars in a function scope (negative)
--FILE--
<?php
declare(declare_vars=1);

function test() {
  var $foo; // Implicit NULL value.
  var_dump($foo); // Never executed because compile error.

  var $bar = 1;
  var_dump($bar); // Never executed because compile error.

  var_dump($baz); // Compile error
}

echo "Start"; // Never executed becuse compile error.
test();
echo "Done"; // Never executed because compile error.
--EXPECTF--

Fatal error: Undeclared variable: baz in %s/declare_vars_func_failure.php on line %d
