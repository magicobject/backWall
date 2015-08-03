<?php
namespace backWallTest;

use backWall\bwPad;
use backWall\bwKeyGenerator;

class bwPadTest extends \PHPUnit_Framework_TestCase {

	public function testBwPadClass() {
		$b=new bwPad();
		$this->assertInstanceOf(bwPad::class,$b);
	}

	public function testBwPad() {
		$b=new bwPad();
		$pad=$b->getPad(199,17);

		$this->assertEquals(208,strlen($pad)); // 208 = Largest multiple of 16 over or = to 199

		$bwkey=new bwKeyGenerator();
		$bwkey->nth(17);
		$start=$bwkey->getTxt();

		$strpad=bin2hex($pad);

		$this->assertEquals($start, substr($strpad,0,32));
	}
}
