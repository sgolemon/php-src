--TEST--
Pipe Operator Variable Variables
--FILE--
<?php

$a = 1;
$b = 'a';
$c = 'b';

var_dump($$b);
var_dump($${'b'});
var_dump($$ {'b'});
var_dump($$$c);
--EXPECT--
int(1)
int(1)
int(1)
int(1)
