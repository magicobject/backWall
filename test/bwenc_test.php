<?php
define('AUTOLOAD_PATH','..');
require_once '../autoload.php';

class bwenc_test extends Test {

	public function testBwEncClass() {
		$b=new bwenc("a",1);
		$this->assertClass('bwenc',$b);
	}

	public function testBwEnc1() {
		$b=new bwenc("Hello World", 1066);
		$encrypted=$b->encrypt();
		$this->assertEqual("db151867666f479df025bb",bin2hex($encrypted));
	}

	public function testBwDec1() {
		$b=new bwenc("db151867666f479df025bb", 1066);
		$decrypted=$b->decrypt();
		$this->assertEqual("Hello World", $decrypted);
	}

	public function testBwEnc2() {
		$b=new bwenc("The quick brown fox jumped over the lazy dog.", 1066);
		$encrypted=$b->encrypt();
		$this->assertEqual("c718112b783a7991e969bda553bb4474ce52485a7bd2624dd646e3f4c7aebfa461bd326d5ca1e778648b518bf7",bin2hex($encrypted));
	}

	public function testBwDec2() {
		$b=new bwenc("c718112b783a7991e969bda553bb4474ce52485a7bd2624dd646e3f4c7aebfa461bd326d5ca1e778648b518bf7", 1066);
		$decrypted=$b->decrypt();
		$this->assertEqual("The quick brown fox jumped over the lazy dog.", $decrypted);
	}

	public function testBwEnc3() {
		$b=new bwenc("The quick brown fox jumped over the lazy dog.", 1066);
		$encrypted=$b->encrypt("6c3e4138b14ea9ef70ea6c");
		$this->assertEqual("bbeef619f801560944079bd6227f6042845aa9b6c3b6fe443a82ed34808f1ec882ea6a927170d9f5d7eb60cf89",bin2hex($encrypted));
	}

	public function testBwDec3() {
		$b=new bwenc("bbeef619f801560944079bd6227f6042845aa9b6c3b6fe443a82ed34808f1ec882ea6a927170d9f5d7eb60cf89", 1066);
		$decrypted=$b->decrypt("6c3e4138b14ea9ef70ea6c");
		$this->assertEqual("The quick brown fox jumped over the lazy dog.", $decrypted);
	}

	public function testBwEnc4() {
		$b=new bwenc("Hello WorldHello World", 1066);
		$encrypted=$b->encrypt();
		$this->assertEqual("db151867666f479df025bb9f59a0463b886a5f087dc3",bin2hex($encrypted));
	}

	public function testBwDec4() {
		$b=new bwenc("db151867666f479df025bb9f59a0463b886a5f087dc3", 1066);
		$decrypted=$b->decrypt();
		$this->assertEqual("Hello WorldHello World", $decrypted);
	}

	public function testBwEnc5() {
		$b=new bwenc("@#!\"£$%^&*()_-{[}]~:;?", 81922066);
		$encrypted=$b->encrypt();
		$this->assertEqual("b94bc154b34ee9e3f3259cfeea39df278c82669386c8ae",bin2hex($encrypted));
	}

	public function testBwDec5() {
		$b=new bwenc("b94bc154b34ee9e3f3259cfeea39df278c82669386c8ae", 81922066);
		$decrypted=$b->decrypt();
		$this->assertEqual("@#!\"£$%^&*()_-{[}]~:;?", $decrypted);
	}

}

$t=new bwenc_test();
