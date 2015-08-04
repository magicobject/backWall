<?php
namespace backWallTest;

use backWall\bwPassword;

class bwPasswordTest extends \PHPUnit_Framework_TestCase {

	public function testEncode() {
		$bwp=new bwPassword();
		$password="gXohZg89@:";
		$encoded=$bwp->encode($password);

		$bwp2=new bwPassword();
		$bwp_decoded = $bwp2->decode($encoded);

		$this->assertEquals($password, $bwp_decoded);

		$password="gXohZg89@:";
		$encoded2=$bwp->encode($password);

		$this->assertNotEquals($encoded, $encoded2);
	}
}
