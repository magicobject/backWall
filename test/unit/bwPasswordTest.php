<?php
namespace backWallTest;

use backWall\bwPassword;
use backWall\HashAlgorithms\MD5HashAlgorithm;

/**
 * Class bwPasswordTest
 * @package backWallTest
 */
class bwPasswordTest extends \PHPUnit_Framework_TestCase {

	public function testEncode() {
		$bwp=new bwPassword(new MD5HashAlgorithm());
		$password="gXohZg89@:";
		$encoded=$bwp->encode($password);

		$bwp2=new bwPassword(new MD5HashAlgorithm());
		$bwp_decoded = $bwp2->decode($encoded);

		$this->assertEquals($password, $bwp_decoded);

		$password="gXohZg89@:";
		$encoded2=$bwp->encode($password);

		$this->assertNotEquals($encoded, $encoded2);
	}
}
