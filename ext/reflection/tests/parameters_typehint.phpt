--TEST--
ReflectionParameter::hasTypehint() / getTypehintText()
--FILE--
<?php
function foo(stdClass $a, array $b, callable $c, $d) { }

$rf = new ReflectionFunction('foo');
foreach ($rf->getParameters() as $idx => $rp) {
  echo "** Parameter $idx\n";
  var_dump($rp->hasTypehint());
  var_dump($rp->getTypehintText());
}
--EXPECT--
** Parameter 0
bool(true)
string(8) "stdClass"
** Parameter 1
bool(true)
string(5) "array"
** Parameter 2
bool(true)
string(8) "callable"
** Parameter 3
bool(false)
string(0) ""
