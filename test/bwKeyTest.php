<?php
namespace backWallTest;

use backWall\bwKeyGenerator;

class bwKeyTest extends \PHPUnit_Framework_TestCase {
	public function testBwKeyClass() {
		$b=new bwKeyGenerator();
		$this->assertInstanceOf(bwKeyGenerator::class,$b);
	}

	public function _test257() {
		$b=new bwKeyGenerator();
		for ($i=0; $i< 260000; $i++) {
			$b->nth($i);
			echo $b->getTxt()."\n";
		}
	}

	public function testBwKeyValues() {
		$b=new bwKeyGenerator();
		$b->nth(1);
		$value=$b->getTxt();
		$this->assertEquals('6903f303c61f07b48c37027f13f8593f',$value);

		$b->nth(2);
		$value=$b->getTxt();
		$this->assertEquals('5a7c5f0948faa2bd775059ff716b229c',$value);

		$b->nth(200000000);
		$value=$b->getTxt();
		$this->assertEquals('88f15724120130557fa3705293b39d89',$value);
	}
}
