--TEST--
Pipe Operator Double use
--FILE--
<?php

$a = "foo"
 |> $$ . $$;
--EXPECTF--
Fatal error: Cannot use $$ twice in a single pipe expression in %s/pipe-004.php on line 4
