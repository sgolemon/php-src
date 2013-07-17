--TEST--
Use of parameter visibility modifiers on function args
--FILE--
<?php

//
// function don't allow promotion
//
function f(public $a) {}

--EXPECTF--
Fatal error: Parameter modifiers not allowed on functions in %s on line %d
