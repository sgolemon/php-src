--TEST--
Double comma in fcall list
--FILE--
<?php
function testFunc() {}
testFunc(1,,2);
--EXPECTF--
Parse error: syntax error, unexpected ',', expecting ')' in %s/Zend/tests/function_arguments_trailing_comma_010.php on line 3
