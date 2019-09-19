--TEST--
Declare Vars in a method scope (positive)
--FILE--
<?php
declare(declare_vars=1);

class C {
  public $prop;

  public function test() {
	// Fine as $this exists in method contexts.
	$this->prop = 'value';

	// Dynamic props not impacted by declare_vars directive
	$this->dynprop = 'value';

    var $foo; // Implicit NULL value.
    var_dump($foo);

    var $bar = 1;
    var_dump($bar);
  }
}

(new C)->test();
echo "Done";
--EXPECTF--
NULL
int(1)
Done
