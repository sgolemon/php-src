--TEST--
Comma in empty fcall list
--FILE--
<?php
phpinfo(,);
--EXPECTF--
Parse error: syntax error, unexpected ',' in %s/Zend/tests/function_arguments_trailing_comma_007.php on line 2
