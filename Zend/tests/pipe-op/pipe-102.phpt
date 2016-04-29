--TEST--
Pipe Operator Operation Ordering
--FILE--
<?php

// Imported from HackLang test: pipevar-4.php

class Wrapper {
  public $val;
  public function __construct(array $val) {
    $this->val = $val;
    var_dump("Make wrapper");
  }
  public function __destruct() {
    var_dump("Destroy wrapper");
  }
}

function beep($x) {
  if ($x instanceof Wrapper) {
    var_dump("beep: <Wrapper>");
  } else if (is_array($x)) {
    var_dump("beep: <array>");
  } else {
    var_dump("beep: ".$x);
  }
  return $x;
}
function wrap($x) {
  return new Wrapper($x);
}
function unwrap($y) {
  return $y->val;
}

function main($bar) {
  $foo = "Hello!";
  $out = array(1, 2, 3)
    |> array_map(function ($x) { return $x + beep(1); }, $$)
    |> array_merge(
      array(50, 60, 70)
        |> array_map(function ($x) { return $x * beep(2); }, $$)
        |> array_filter($$, function ($x) { return $x != beep(100); }),
      $$)
    |> array_filter($$, function ($x) { return $x != beep(3); })
    |> wrap($$)
    |> beep($$)
    |> unwrap($$)
    |> beep($$)
    |> array_map(function ($x) { return "STR: $x"; }, $$);

  var_dump($foo);
  var_dump($out);
  var_dump($bar);
}

main("Goodbye");

--EXPECT--
string(7) "beep: 1"
string(7) "beep: 1"
string(7) "beep: 1"
string(7) "beep: 2"
string(7) "beep: 2"
string(7) "beep: 2"
string(9) "beep: 100"
string(9) "beep: 100"
string(9) "beep: 100"
string(7) "beep: 3"
string(7) "beep: 3"
string(7) "beep: 3"
string(7) "beep: 3"
string(7) "beep: 3"
string(12) "Make wrapper"
string(15) "beep: <Wrapper>"
string(15) "Destroy wrapper"
string(13) "beep: <array>"
string(6) "Hello!"
array(4) {
  [0]=>
  string(8) "STR: 120"
  [1]=>
  string(8) "STR: 140"
  [2]=>
  string(6) "STR: 2"
  [4]=>
  string(6) "STR: 4"
}
string(7) "Goodbye"

