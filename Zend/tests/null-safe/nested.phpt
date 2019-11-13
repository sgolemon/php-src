--TEST--
Deep nested usages of the nullsafe operator
--FILE--
<?php

final class Foo {
  public ?Bar $bar;

  public function __construct(
    ?Bar $bar
  ) {
    $this->bar = $bar;
  }
}

final class Bar {
  public ?Foo $foo = null;
}

$bar = new Bar();
$foo = new Foo($bar);
$bar->foo = $foo;

assert($bar === $bar?->foo?->bar?->foo?->bar);

echo 'OK';
--EXPECTF--
OK
