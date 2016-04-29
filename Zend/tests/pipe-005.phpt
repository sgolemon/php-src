--TEST--
Pipe Operator Not a pipe
--FILE--
<?php

echo $$;
--EXPECTF--
Fatal error: Cannot use $$ outside of a pipe expression in %s/pipe-005.php on line 3
