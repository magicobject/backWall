<?php
define('AUTOLOAD_PATH','..');
require_once '../autoload.php';

class bwpad_test extends Test {

	public function testBwPadClass() {
		$b=new bwpad();
		$this->assertClass('bwpad',$b);
	}

	public function testBwPad() {
		$b=new bwpad();
		$pad=$b->getPad(199,17);

		$this->assertEqual(208,strlen($pad)); // 208 = Largest multiple of 16 over or = to 199

		$bwkey=new bwkey();
		$bwkey->nth(17);
		$start=$bwkey->getTxt();

		$strpad=bin2hex($pad);

		$this->assertEqual($start, substr($strpad,0,32));
	}
}

$t=new bwpad_test();
