--TEST--
Leading comma in fcall list
--FILE--
<?php
strlen(,"123");
--EXPECTF--
Parse error: syntax error, unexpected ',' in %s/Zend/tests/function_arguments_trailing_comma_009.php on line 2
