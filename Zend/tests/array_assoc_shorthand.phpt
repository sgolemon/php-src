--TEST--
Shorthand initialization and destructuring of associative arrays
--FILE--
<?php

$x = 1;
$y = 2;
$z = 3;

// Initialization
var_dump([
	=> $x,
	'other_x' => $x,
	=> $y,
	[ => $y, => $z ],
]);

// Destructuring
[ =>$a, =>$b ] = [
	'a' => 5,
	'b' => 6,
];
var_dump($a, $b);

--EXPECT--
array(4) {
  ["x"]=>
  int(1)
  ["other_x"]=>
  int(1)
  ["y"]=>
  int(2)
  [0]=>
  array(2) {
    ["y"]=>
    int(2)
    ["z"]=>
    int(3)
  }
}
int(5)
int(6)
