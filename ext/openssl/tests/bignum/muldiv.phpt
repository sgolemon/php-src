--TEST--
BigNum multiplication and division
--SKIPIF--
<?php
if (!extension_loaded('openssl')) echo 'skip';
--FILE--
<?php

$bn = new \OpenSSL\BigNum(42);
var_dump($bn->mul(2)->toDec());
var_dump($bn->div(4)[0]->toDec());
var_dump($bn->div(4)[1]->toDec());
var_dump($bn->intdiv(5)->toDec());
var_dump($bn->mod(8)->toDec());

--EXPECT--
string(2) "84"
string(2) "10"
string(1) "2"
string(1) "8"
string(1) "2"
