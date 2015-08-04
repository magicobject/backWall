<?php

namespace backWall;

use backWall\HashAlgorithms\HashAlgorithmInterface;

/**
 * Class bwPassword
 * @package backWall
 */
class bwPassword {

	/** @var  HashAlgorithmInterface */
	private $hashAlgorithm;

	public function __construct(HashAlgorithmInterface $hashAlgorithm)
	{
		$this->hashAlgorithm = $hashAlgorithm;
	}

	/**
	 * @param $password
	 * @return string
	 */
	public function decode($password) {
		list($keyNumber,$password) = preg_split('/:/',$password);
		$encryptor=new bwMessageEncryptor($password,$keyNumber, $this->hashAlgorithm);
		return $encryptor->decrypt();
	}

	/**
	 * @param $password
	 * @return string
	 */
	public function encode($password) {
		$keyNumber=$this->_generate_key_number();
		$encryptor=new bwMessageEncryptor($password, $keyNumber, $this->hashAlgorithm);
		return $keyNumber.':'.bin2hex($encryptor->encrypt());
	}

	/**
	 * @return int
	 */
	private function _generate_key_number() {
			return rand(1,1024*1024);
	}
}
