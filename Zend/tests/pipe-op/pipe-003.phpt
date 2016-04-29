--TEST--
Pipe Operator Unused temp expression
--FILE--
<?php

$a = time() + 1
  |> "foo";

var_dump($a);
--EXPECT--
string(3) "foo"
