--TEST--
Pipe Operator Nested pipes
--FILE--
<?php

var_dump(
  "Baz" |> ("Foo" |> $$ . "Bar") . $$
);
--EXPECT--
string(9) "FooBarBaz"
