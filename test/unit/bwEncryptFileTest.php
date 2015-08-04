<?php

namespace backWallTest;

use backWall\bwFileEncryptor;

/**
 * Class bwEncryptFileTest
 * @package backWallTest
 */
class bwEncryptFileTest extends \PHPUnit_Framework_TestCase {

	public function testBwEncFileClass() {
		$b=new bwFileEncryptor("/dev/null");
		$this->assertInstanceOf(bwFileEncryptor::class, $b);
	}

	public function testBwEncFile() {
		$encfile=new bwFileEncryptor("fixtures/test_enc_file","f9H%3&l[@J3|");
		$encfile->encrypt();
	}

	public function testBwDecFile() {
		$encfile=new bwFileEncryptor("fixtures/test_enc_file","f9H%3&l[@J3|");
		$encfile->decrypt();
	}
}
