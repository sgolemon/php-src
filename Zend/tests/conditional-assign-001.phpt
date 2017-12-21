--TEST--
Basic tests for conditional assignment ?= :
--FILE--
<?php

$x = 10;
$x ?= ($x < 5) : 'should get this';
var_dump($x);

$x = 10;
$x ?= ($x > 5) : 'should not get this';
var_dump($x);

/* As above, but consuming result node */

$x = 10;
var_dump($x ?= ($x < 5) : 'should get this');

$x = 10;
var_dump($x ?= ($x > 5) : 'should not get this');

--EXPECT--
string(15) "should get this"
int(10)
string(15) "should get this"
int(10)
