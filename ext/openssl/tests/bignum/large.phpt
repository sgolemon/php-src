--TEST--
Numbers larger than int64_t
--SKIPIF--
<?php
if (!extension_loaded('openssl')) echo 'skip';
--FILE--
<?php

$large = new \OpenSSL\BigNum('0x10000000000000000');
var_dump($large->toDec());
var_dump($large->toHex());
var_dump($large->mul(-1)->toDec());
var_dump($large->mul($large)->toDec());
var_dump($large->mul($large)->toHex());
var_dump($large->intdiv('4294967296')->toDec());

--EXPECTF--
string(20) "18446744073709551616"
string(18) "010000000000000000"
string(21) "-18446744073709551616"
string(39) "340282366920938463463374607431768211456"
string(34) "0100000000000000000000000000000000"
string(10) "4294967296"
