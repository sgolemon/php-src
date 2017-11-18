--TEST--
Comprehension usage with capture
--FILE--
<?php

$a = [ 1, 2, 3];
$mul = 3;

$c = [ for $a as $v yield $mul * $v use ($mul) ];

foreach($c as $v) {
	var_dump($v);
}
--EXPECT--
int(3)
int(6)
int(9)
