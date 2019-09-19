--TEST--
Declare Vars in a method scope (negative)
--FILE--
<?php
declare(declare_vars=1);

class C {
  public $prop;

  public function test() {
    var_dump($baz); // Compile error
  }
}

echo "Start"; // Never executed becuse compile error.
(new C)->test();
echo "Done"; // Never executed because compile error.
--EXPECTF--

Fatal error: Undeclared variable: baz in %s/declare_vars_method_failure.php on line %d
