--TEST--
Declare Vars equals 0
--FILE--
<?php
declare(declare_vars=0);

// Just verify that undefined var notice works for BC.

echo $foo;
function bar() {
  echo $baz;
}
bar();

class qux {
  function bling() {
    echo $blong;
  }
}
(new qux)->bling();

echo "Done";
--EXPECTF--

Notice: Undefined variable: foo in %s/declare_vars_zero.php on line %d

Notice: Undefined variable: baz in %s/Zend/tests/declare_vars_zero.php on line %d

Notice: Undefined variable: blong in %s/declare_vars_zero.php on line %d
Done
