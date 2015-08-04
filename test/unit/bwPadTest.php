<?php
namespace backWallTest;

use backWall\bwPad;
use backWall\bwKeyGenerator;
use backWall\HashAlgorithms\MD5HashAlgorithm;

/**
 * Class bwPadTest
 * @package backWallTest
 */
class bwPadTest extends \PHPUnit_Framework_TestCase {

	/** @var  bwPad */
	private $bwPad;

	public function setup()
	{
		$this->bwPad = new bwPad('', new MD5HashAlgorithm());
	}

	public function testBwPadClass() {
		$this->assertInstanceOf(bwPad::class,$this->bwPad);
	}

	public function testBwPad() {

		$pad=$this->bwPad->getPad(199,17);

		$this->assertEquals(208,strlen($pad)); // 208 = Largest multiple of 16 over or = to 199

		$bwkey=new bwKeyGenerator(null, new MD5HashAlgorithm());
		$bwkey->generateNthKey(17);
		$start=$bwkey->getText();

		$strpad=bin2hex($pad);

		$this->assertEquals($start, substr($strpad,0,32));
	}
}
