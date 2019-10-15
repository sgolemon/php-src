--TEST--
Using null-safe call with non object/null
--FILE--
<?php

function show(\Error $e) {
  echo 'Error: ', $e->getMessage(), ' on line ', $e->getLine(), PHP_EOL;
}
$types = [ true, false, 0, 0.0, 1, 0.1, "", "hello", [], [null], STDIN ];

echo "Simple var\n";
foreach ($types as $type) {
  try { $type?->getSelf(); } catch (\Error $e) { show($e); }
}

class A {
  public $prop;
}

echo "Class prop\n";
$a = new A;
foreach ($types as $type) {
  $a->prop = $type;
  try { $a->prop?->getSelf(); } catch (\Error $e) { show($e); }
}

echo "Variable variables\n";
$b = 'c';
foreach ($types as $type) {
  $$b = $type;
  try { $$b?->getSelf(); } catch (\Error $e) { show($e); }
}
--EXPECTF--
Simple var
Error: Call to a member function getSelf() on bool on line %d
Error: Call to a member function getSelf() on bool on line %d
Error: Call to a member function getSelf() on int on line %d
Error: Call to a member function getSelf() on float on line %d
Error: Call to a member function getSelf() on int on line %d
Error: Call to a member function getSelf() on float on line %d
Error: Call to a member function getSelf() on string on line %d
Error: Call to a member function getSelf() on string on line %d
Error: Call to a member function getSelf() on array on line %d
Error: Call to a member function getSelf() on array on line %d
Error: Call to a member function getSelf() on resource on line %d
Class prop
Error: Call to a member function getSelf() on bool on line %d
Error: Call to a member function getSelf() on bool on line %d
Error: Call to a member function getSelf() on int on line %d
Error: Call to a member function getSelf() on float on line %d
Error: Call to a member function getSelf() on int on line %d
Error: Call to a member function getSelf() on float on line %d
Error: Call to a member function getSelf() on string on line %d
Error: Call to a member function getSelf() on string on line %d
Error: Call to a member function getSelf() on array on line %d
Error: Call to a member function getSelf() on array on line %d
Error: Call to a member function getSelf() on resource on line %d
Variable variables
Error: Call to a member function getSelf() on bool on line %d
Error: Call to a member function getSelf() on bool on line %d
Error: Call to a member function getSelf() on int on line %d
Error: Call to a member function getSelf() on float on line %d
Error: Call to a member function getSelf() on int on line %d
Error: Call to a member function getSelf() on float on line %d
Error: Call to a member function getSelf() on string on line %d
Error: Call to a member function getSelf() on string on line %d
Error: Call to a member function getSelf() on array on line %d
Error: Call to a member function getSelf() on array on line %d
Error: Call to a member function getSelf() on resource on line %d
