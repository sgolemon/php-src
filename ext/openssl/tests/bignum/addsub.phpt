--TEST--
BigNum addition and subtraction
--SKIPIF--
<?php
if (!extension_loaded('openssl')) echo 'skip';
--FILE--
<?php

$bn = new \OpenSSL\BigNum(42);
var_dump($bn->add(new \OpenSSL\BigNum(2))->toDec());
var_dump($bn->add(new \OpenSSL\BigNum(-2))->toDec());
var_dump($bn->add(3)->toDec());
var_dump($bn->add(-3)->toDec());

var_dump($bn->sub(new \OpenSSL\BigNum(2))->toDec());
var_dump($bn->sub(new \OpenSSL\BigNum(-2))->toDec());

try {
  $bn->add($bn->add("foo")->toDec());
} catch (\TypeError $e) {
  var_dump($e->getMessage());
}
--EXPECT--
string(2) "44"
string(2) "40"
string(2) "45"
string(2) "39"
string(2) "40"
string(2) "44"
string(122) "OpenSSL\BigNum::add() expects parameter 1 to be an instance of OpenSSL\BigNum or numeric integer, non-numeric string given"
