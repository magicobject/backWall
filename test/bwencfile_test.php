<?php
define('AUTOLOAD_PATH','..');
require_once '../autoload.php';

class bwEncFile_test extends Test {

	public function testBwEncFileClass() {
		$b=new bwEncFile("/dev/null");
		$this->assertClass('bwEncFile',$b);
	}

	public function testBwEncFile() {
		$encfile=new bwEncFile("test_enc_file","f9H%3&l[@J3|");
		$encfile->encrypt();
	}

	public function testBwDecFile() {
		$encfile=new bwEncFile("test_enc_file","f9H%3&l[@J3|");
		$encfile->decrypt();
	}
}

$t=new bwEncFile_test();
