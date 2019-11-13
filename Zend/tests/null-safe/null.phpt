--TEST--
usages of the nullsafe operator on null value
--FILE--
<?php

function qux(): ?ArrayObject {
  return null;
}

$null = null;

$ret[] = $null?->foo();

$ret[] = ($null)?->bar();

$ret[] = (null)?->baz();

$ret[] = ($baz ?? qux()?->bar ?? null)
    ?->qux;

foreach($ret as $_) {
  var_dump($_);
}
--EXPECTF--
NULL
NULL
NULL
NULL
