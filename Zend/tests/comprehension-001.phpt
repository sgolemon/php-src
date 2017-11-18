--TEST--
Comprehension usage basic
--FILE--
<?php

$a = [ 1, 2, 3];

$c = [ for $a as $v yield 2 * $v ];

foreach($c as $v) {
	var_dump($v);
}
--EXPECT--
int(2)
int(4)
int(6)
