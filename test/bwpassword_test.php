<?php
define('AUTOLOAD_PATH','..');
require_once '../autoload.php';

class bwpassword_test extends Test {

	public function testEncode() {
		$bwp=new bwpassword();
		$password="gXohZg89@:";
		$encoded=$bwp->encode($password);

		// echo "$encoded\n";

		$bwp2=new bwpassword();
		$bwp_decoded = $bwp2->decode($encoded);

		$this->assertEqual($password, $bwp_decoded);
		// echo "$password, $bwp_decoded\n";

		$bwp2=new bwpassword();
		$password="gXohZg89@:";
		$encoded2=$bwp->encode($password);

		$this->assertDifferent($encoded, $encoded2);
	}
}

$t=new bwpassword_test();
