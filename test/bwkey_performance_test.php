<?php
define('AUTOLOAD_PATH','..');
require_once '../autoload.php';

class bwkey_performance_test extends Test {

	public function _test2() {
		$b=new bwkey();
		for ($i=128; $i< 130; $i++) {
			$b->nth($i);
			echo $b->getTxt()."\n";
		}
	}

	public function test250000() {
		$b=new bwkey();
		for ($i=0; $i< 250000; $i++) {
			$b->nth($i);
			echo $b->getTxt()."\n";
		}
	}
}

$t=new bwkey_performance_test();
