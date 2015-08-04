<?php
namespace backWallTest;

use backWall\bwKeyGenerator;
use backWall\HashAlgorithms\MD5HashAlgorithm;

/**
 * Class bwKeyGeneratorTest
 * @package backWallTest
 */
class bwKeyGeneratorTest extends \PHPUnit_Framework_TestCase {

    /** @var  bwKeyGenerator */
    private $bwKeyGenerator;

    public function setup()
    {
        $this->bwKeyGenerator = new bwKeyGenerator('', new MD5HashAlgorithm());
    }

	public function testBwKeyClass() {
		$this->assertInstanceOf(bwKeyGenerator::class,$this->bwKeyGenerator);
	}

	public function testBwKeyValues() {

		$this->bwKeyGenerator->generateNthKey(1);
		$value=$this->bwKeyGenerator->getText();
		$this->assertEquals('6903f303c61f07b48c37027f13f8593f',$value);

		$this->bwKeyGenerator->generateNthKey(2);
		$value=$this->bwKeyGenerator->getText();
		$this->assertEquals('5a7c5f0948faa2bd775059ff716b229c',$value);

		$this->bwKeyGenerator->generateNthKey(200000000);
		$value=$this->bwKeyGenerator->getText();
		$this->assertEquals('88f15724120130557fa3705293b39d89',$value);
	}
}
