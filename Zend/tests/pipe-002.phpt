--TEST--
Pipe Operator Fcall return
--FILE--
<?php

$a = strtolower("FOO")
  |> ucwords($$)
  |> $$ . "bar"
  ;

var_dump($a);
--EXPECT--
string(6) "Foobar"
