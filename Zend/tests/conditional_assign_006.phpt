--TEST--
Conditional Assignment Dim Multi
--FILE--
<?php

$x[1][2][3][4] ?:= 'hello world';
var_dump($x);
$x[1][2][5][6] ?:= 'goodbye';
var_dump($x);

unset($x);

$y = 1;
$x = array(&$y);
$x[0] ?:= 2;
var_dump($y);

$y = 0;
$x[0] ?:= 3;
var_dump($y);


--EXPECTF--
Notice: Undefined variable: x in %s/conditional_assign_006.php on line 3

Notice: Undefined offset: 1 in %s/conditional_assign_006.php on line 3

Notice: Undefined offset: 2 in %s/conditional_assign_006.php on line 3

Notice: Undefined offset: 3 in %s/conditional_assign_006.php on line 3

Notice: Undefined offset: 4 in %s/conditional_assign_006.php on line 3
array(1) {
  [1]=>
  array(1) {
    [2]=>
    array(1) {
      [3]=>
      array(1) {
        [4]=>
        string(11) "hello world"
      }
    }
  }
}

Notice: Undefined offset: 5 in %s/conditional_assign_006.php on line 5

Notice: Undefined offset: 6 in %s/conditional_assign_006.php on line 5
array(1) {
  [1]=>
  array(1) {
    [2]=>
    array(2) {
      [3]=>
      array(1) {
        [4]=>
        string(11) "hello world"
      }
      [5]=>
      array(1) {
        [6]=>
        string(7) "goodbye"
      }
    }
  }
}
int(1)
int(3)
