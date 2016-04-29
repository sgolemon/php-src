--TEST--
Pipe Operator Nested expressions
--FILE--
<?php

// Imported from HackLang test: pipevar-3.php
function main($bar) {
  $foo = "Hello!";
  array(1, 2, 3)
    |> array_map(function($x) { return $x + 1; }, $$)
    |> array_merge(
      array(50, 60, 70)
        |> array_map(function ($x) { return $x * 2; }, $$)
        |> array_filter($$, function ($x) { return $x != 100; }),
      $$)
    |> var_dump($$);

  var_dump($foo);
  var_dump($bar);
}

main("Goodbye");

--EXPECT--
array(5) {
  [0]=>
  int(120)
  [1]=>
  int(140)
  [2]=>
  int(2)
  [3]=>
  int(3)
  [4]=>
  int(4)
}
string(6) "Hello!"
string(7) "Goodbye"

