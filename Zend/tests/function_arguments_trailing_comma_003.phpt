--TEST--
Comma in empty argument list
--FILE--
<?php
function globalFunc(,) {
}
--EXPECTF--
Parse error: syntax error, unexpected ',', expecting '&' or variable (T_VARIABLE) in %s/Zend/tests/function_arguments_trailing_comma_003.php on line 2
