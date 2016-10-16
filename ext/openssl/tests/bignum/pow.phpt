--TEST--
BigNum exponentiation
--SKIPIF--
<?php
if (!extension_loaded('openssl')) echo 'skip';
--FILE--
<?php

$bn = new \OpenSSL\BigNum(2);
var_dump($bn->pow(3)->toDec());
var_dump($bn->powmod(3, 5)->toDec());

--EXPECT--
string(1) "8"
string(1) "3"
