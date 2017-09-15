--TEST--
PipeOp Chain
--FILE--
<?php

$x = "Hello"
  |> 'strtoupper'
  |> function($x) { return $x . ' world'; }
  |> 'strrev';

var_dump($x);
--EXPECT--
string(11) "dlrow OLLEH"
