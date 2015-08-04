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
			$b->generateNthKey($i);
			echo $b->getText()."\n";
		}
	}

	public function testBwKeyValues() {
		$b=new bwKeyGenerator();
		$b->generateNthKey(1);
		$value=$b->getText();
		$this->assertEquals('6903f303c61f07b48c37027f13f8593f',$value);

		$b->generateNthKey(2);
		$value=$b->getText();
		$this->assertEquals('5a7c5f0948faa2bd775059ff716b229c',$value);

		$b->generateNthKey(200000000);
		$value=$b->getText();
		$this->assertEquals('88f15724120130557fa3705293b39d89',$value);
	}
}
