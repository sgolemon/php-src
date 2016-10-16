--TEST--
BigNum miscellaneous
--SKIPIF--
<?php
if (!extension_loaded('openssl')) echo 'skip';
--FILE--
<?php

$bn = new \OpenSSL\BigNum(42);
var_dump($bn->cmp("0x29"));
var_dump($bn->cmp("0x2A"));
var_dump($bn->cmp("0x2B"));
var_dump($bn->gcd(15)->toDec());
var_dump($bn->shr(1)->toDec());
var_dump($bn->shl(1)->toDec());
--EXPECT--
int(1)
int(0)
int(-1)
string(1) "3"
string(2) "21"
string(2) "84"
