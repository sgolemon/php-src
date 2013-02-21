--TEST--
Double trailing commas in fcall argument list
--FILE--
<?php
strlen(123,,);
--EXPECTF--
Parse error: syntax error, unexpected ',', expecting ')' in %s/Zend/tests/function_arguments_trailing_comma_008.php on line 2
