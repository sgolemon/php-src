--TEST--
Pass various bad inputs to constructors and methods
--SKIPIF--
<?php
if (!extension_loaded('openssl')) echo 'skip';
--FILE--
<?php

$methods = [ 'add', 'sub', 'mul', 'div', 'intdiv', 'mod', 'pow' ];
$values = [ 'apple', '42banana', '3.14', '0xcarrot' ];

foreach ($values as $value) {
  try {
    $bn = new \OpenSSL\BigNum($value);
    echo "OK  ctor($value)\n";
  } catch (\TypeError $e) {
    echo "BAD ctor($value)\n";
  }
}

$bn = new \OpenSSL\BigNum(2);
foreach ($methods as $method) {
  foreach ($values as $value) {
    try {
      $bn->$method($value);
      echo "OK  $method($value)\n";
    } catch (\TypeError $e) {
      echo "BAD $method($value)\n";
    }
  }
}
--EXPECT--
BAD ctor(apple)
BAD ctor(42banana)
BAD ctor(3.14)
BAD ctor(0xcarrot)
BAD add(apple)
BAD add(42banana)
BAD add(3.14)
BAD add(0xcarrot)
BAD sub(apple)
BAD sub(42banana)
BAD sub(3.14)
BAD sub(0xcarrot)
BAD mul(apple)
BAD mul(42banana)
BAD mul(3.14)
BAD mul(0xcarrot)
BAD div(apple)
BAD div(42banana)
BAD div(3.14)
BAD div(0xcarrot)
BAD intdiv(apple)
BAD intdiv(42banana)
BAD intdiv(3.14)
BAD intdiv(0xcarrot)
BAD mod(apple)
BAD mod(42banana)
BAD mod(3.14)
BAD mod(0xcarrot)
BAD pow(apple)
BAD pow(42banana)
BAD pow(3.14)
BAD pow(0xcarrot)
