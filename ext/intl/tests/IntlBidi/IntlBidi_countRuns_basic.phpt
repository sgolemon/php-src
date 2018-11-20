--TEST--
Test for IntlBidi countRuns
--CREDITS--
Timo Scholz
<timo.scholz@setasign.com>
--SKIPIF--
<?php if (!extension_loaded('intl')) print 'skip'; ?>
<?php if ( !extension_loaded('mbstring')) print 'skip'; ?>
--FILE--
<?php

include 'IntlBidi_ut_common.inc';

function doTest($string) {
  $bidi = new IntlBidi();

  $bidi->setPara($string, 0);

  $runCount = $bidi->countRuns();
  $len = $bidi->getLength();

  $run = new IntlBidi();
  for ($logicalIndex = 0; $logicalIndex < $len; ) {
    $logicalIndex = $bidi->getLogicalRun($logicalIndex)['limit'];

    if (--$runCount < 0)  {
      throw new RuntimeException('Wrong number of runs compared to Bidi.countRuns()');
    }
  }
  if ($runCount > 0) {
    throw new RuntimeException('Wrong number of runs compared to Bidi.countRuns()');
  }

  var_dump($runCount);
}

doTest("Testen");
doTest(pseudoToU8('del(KC)add(K.C.&)'));
doTest(pseudoToU8('del(QDVT) add(BVDL)'));
doTest(pseudoToU8('del(PQ)add(R.S.)T)U.&'));
doTest(pseudoToU8('del(LV)add(L.V.) L.V.&'));
doTest(pseudoToU8('day  0  R  DPDHRVR dayabbr'));
doTest(pseudoToU8('day  1  H  DPHPDHDA dayabbr'));
doTest(pseudoToU8('day  2   L  DPBLENDA dayabbr'));
doTest(pseudoToU8('day  3  J  DPJQVM  dayabbr'));
doTest(pseudoToU8('day  4   I  DPIQNF    dayabbr'));
doTest(pseudoToU8('day  5  M  DPMEG  dayabbr'));
doTest(pseudoToU8('helloDPMEG'));
doTest(pseudoToU8('hello WXY'));
?>
==DONE==
--EXPECT--
int(0)
int(0)
int(0)
int(0)
int(0)
int(0)
int(0)
int(0)
int(0)
int(0)
int(0)
int(0)
int(0)
==DONE==
