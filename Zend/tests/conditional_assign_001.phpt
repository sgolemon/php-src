--TEST--
Conditional Assignment Basic
--FILE--
<?php

$x = 5;
$x ?:= 6;
var_dump($x);

$x = null;
$x ?:= 7;
var_dump($x);

$x = false;
$x ?:= 8;
var_dump($x);

$x = '';
$x ?:= 9;
var_dump($x);

$x = 0;
$x ?:= 10;
var_dump($x);

$y ?:= 11;
var_dump($y);

--EXPECTF--
int(5)
int(7)
int(8)
int(9)
int(10)

Notice: Undefined variable: y in %s/conditional_assign_001.php on line 23
int(11)
