--TEST--
Double trailing comma in argument list
--FILE--
<?php
function globalFunc($foo,,) {
}
--EXPECTF--
Parse error: syntax error, unexpected ',', expecting '&' or variable (T_VARIABLE) in %s/Zend/tests/function_arguments_trailing_comma_004.php on line 2
