--TEST--
IntlCharsetDetector Basic Usage
--SKIPIF--
<?php
if (!extension_loaded('intl')) echo 'skip';
--FILE--
<?php

$texts = array(
  // Spanish for "Elephants are amazing": Los elefantes son increíbles
  "Los elefantes son incre\u{ed}bles" => array('ISO-8859-1'),

  // Hebrew for "I love double colon": אני אוהב את המעי גס הכפול
  "\u{5d0}\u{5e0}\u{5d9} \u{5d0}\u{5d5}\u{5d4}\u{5d1} \u{5d0}\u{5ea} ".
  "\u{5d4}\u{5de}\u{5e2}\u{5d9} \u{5d2}\u{5e1} \u{5d4}\u{5db}\u{5e4}\u{5d5}\u{5dc}" => array('ISO-8859-8'),
);

foreach ($texts as $text => $encodings) {
  foreach ($encodings as $encoding) {
    echo "** $encoding\n";
    $test = UConverter::transcode($text, $encoding, 'utf-8');
    $detect = (new IntlCharsetDetector($test))->detect();
    var_dump($detect['name'], $detect['language']);
  }
}

--EXPECTF--
** ISO-8859-1
string(10) "ISO-8859-1"
string(2) "es"
** ISO-8859-8
string(12) "ISO-8859-8-I"
string(2) "he"
