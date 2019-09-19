--TEST--
Declare Vars, variable variables
--FILE--
<?php
declare(declare_vars=1);

var $foo = 'bar';
var $ex;

try {
  $foo = 'bar';
  var_dump($$foo);
} catch (UndeclaredVariableError $ex) {
  echo $ex->getMessage(), "\n";
}

try {
  $foo = 'ex';
  unset($$foo);
} catch (IllegalUnsetError $ex) {
  echo $ex->getMessage(), "\n";
}

var $baz = true;
$foo = 'baz';
var_dump($$foo);

--EXPECT--
Undeclared variable: bar
Declared var $ex may not be unset
bool(true)
