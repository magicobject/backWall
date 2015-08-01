<?php

class bwpassword {
	public function __construct() {
	}

	public function decode($pw) {
		list($key_no,$pw) = preg_split('/:/',$pw);
		$enc=new bwenc($pw,$key_no);
		return $enc->decrypt();
	}


	public function encode($pw) {
		$key_no=$this->_generate_key_number();
		$enc=new bwenc($pw,$key_no);
		return $key_no.':'.bin2hex($enc->encrypt());
	}

	private function _generate_key_number() {
			return rand(1,1024*1024);
	}

}
