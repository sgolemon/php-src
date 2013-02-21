--TEST--
Repeated commas mid-argument list
--FILE--
<?php
function globalFunc($a,,$b) {
}
--EXPECTF--
Parse error: syntax error, unexpected ',', expecting '&' or variable (T_VARIABLE) in %s/Zend/tests/function_arguments_trailing_comma_006.php on line 2
