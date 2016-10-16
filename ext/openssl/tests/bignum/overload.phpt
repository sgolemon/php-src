--TEST--
Operator Overloading
--SKIPIF--
<?php
if (!extension_loaded('openssl')) echo 'skip';
--FILE--
<?php

$bn = new \OpenSSL\BigNum(42);

echo $bn + 1, "\n";
echo 5 + $bn, "\n";
echo $bn - 1, "\n";
echo $bn - (new \OpenSSL\BigNum(3)), "\n";
echo $bn * 3, "\n";
echo $bn / 3, "\n";
echo $bn ** 2, "\n";
echo $bn % 5, "\n";
echo $bn >> 1, "\n";
echo $bn << 1, "\n";

echo $bn += 1, "\n";

--EXPECT--
43
47
41
39
126
14
1764
2
21
84
43
