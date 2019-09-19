--TEST--
Declare Vars in the global scope (positive)
--FILE--
<?php
declare(declare_vars=1);

var $foo; // Implicit NULL value.
var_dump($foo);

var $bar = 1;
var_dump($bar);

echo "Done";
--EXPECTF--
NULL
int(1)
Done
