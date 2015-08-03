<?php

namespace backWallTest;

use backWall\bwKeyGenerator;

class bwKeyPerformanceTest extends \PHPUnit_Framework_TestCase {

	public function _test2() {
		$b=new bwKeyGenerator();
		for ($i=128; $i< 130; $i++) {
			$b->nth($i);
			echo $b->getTxt()."\n";
		}
	}

	public function test250000() {
		$b=new bwKeyGenerator();
		for ($i=0; $i< 250000; $i++) {
			$b->nth($i);
			echo $b->getTxt()."\n";
		}
	}
}
