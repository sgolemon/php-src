--TEST--
Basic OpenSSL\BigNum tests
--SKIPIF--
<?php
if (!extension_loaded('openssl')) echo 'skip';
--FILE--
<?php

$bn = new \OpenSSL\BigNum(123);
var_dump($bn->toDec());
var_dump((string)$bn);
var_dump($bn->toHex());
var_dump(bin2hex($bn->toBin()));

var_dump($bn);
--EXPECTF--
string(3) "123"
string(3) "123"
string(2) "7B"
string(2) "7b"
object(OpenSSL\BigNum)#%d (2) {
  ["dec"]=>
  string(3) "123"
  ["hex"]=>
  string(2) "7B"
}
