--TEST--
Pipe Operator Basic
--FILE--
<?php

$b = [ "one", "two", "three", "four" ]
  |> array_filter($$, function ($x) { return strlen($x) % 2; })
  |> array_map(function ($x) { return "$x is ".strlen($x)." bytes long"; }, $$)
  ;

print_r($b);
--EXPECT--
Array
(
    [0] => one is 3 bytes long
    [1] => two is 3 bytes long
    [2] => three is 5 bytes long
)
