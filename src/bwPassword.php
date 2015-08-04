<?php

namespace backWall;

/**
 * Class bwPassword
 * @package backWall
 */
class bwPassword {

	/**
	 * @param $password
	 * @return string
	 */
	public function decode($password) {
		list($keyNumber,$password) = preg_split('/:/',$password);
		$encryptor=new bwMessageEncryptor($password,$keyNumber);
		return $encryptor->decrypt();
	}

	/**
	 * @param $password
	 * @return string
	 */
	public function encode($password) {
		$keyNumber=$this->_generate_key_number();
		$encryptor=new bwMessageEncryptor($password,$keyNumber);
		return $keyNumber.':'.bin2hex($encryptor->encrypt());
	}

	/**
	 * @return int
	 */
	private function _generate_key_number() {
			return rand(1,1024*1024);
	}

}
