--TEST--
__cmp() magic method
--FILE--
<?php

class C {
	public $x = 0;
	public $y = 0;

	public function __construct($x, $y) {
		$this->x = $x;
		$this->y = $y;
	}

	public function __cmp($rhs) {
		if (!isset($rhs->x)) {
			return 1;
		}
		return $this->x <=> $rhs->x;
	}
}

var_dump((new C(1, 2)) <=> (new C(1, 2)));
var_dump((new C(1, 2)) <=> (new C(2, 2)));
var_dump((new C(1, 2)) <=> (new C(1, 1)));
var_dump((new C(0, 0)) <=> ((object)[]));
var_dump((new C(1, 2)) <=> ((object)['x'=>1]));
--EXPECT--
int(0)
int(-1)
int(0)
int(1)
int(0)
