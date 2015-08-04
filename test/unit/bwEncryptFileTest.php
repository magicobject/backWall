<?php

namespace backWallTest;

use backWall\bwFileEncryptor;
use backWall\HashAlgorithms\MD5HashAlgorithm;

/**
 * Class bwEncryptFileTest
 * @package backWallTest
 */
class bwEncryptFileTest extends \PHPUnit_Framework_TestCase {

	public function testBwEncFileClass() {
		$b=new bwFileEncryptor("/dev/null", null, new MD5HashAlgorithm());
		$this->assertInstanceOf(bwFileEncryptor::class, $b);
	}

	public function testBwEncFile() {
		$encfile=new bwFileEncryptor("fixtures/test_enc_file","f9H%3&l[@J3|", new MD5HashAlgorithm());
		$encfile->encrypt();
	}

	public function testBwDecFile() {
		$encfile=new bwFileEncryptor("fixtures/test_enc_file","f9H%3&l[@J3|", new MD5HashAlgorithm());
		$encfile->decrypt();
	}
}
