<?php

namespace backWall;

use backWall\HashAlgorithms\HashAlgorithmInterface;
/**
 * Class bwMessageEncryptor
 * @package backWall
 */
class bwMessageEncryptor {

	/** @var  String */
	private $message;

	/** @var  integer */
	private $keyNumber;

	/** @var  string */
	private $encrypted;

	/** @var  HashAlgorithmInterface */
	private $hashAlgorithm;

	/**
	 * @param $msg
	 * @param $key_number
	 */
	public function __construct($msg, $key_number, HashAlgorithmInterface $hashAlgorithm) {
		$this->message=$msg;
		$this->keyNumber=$key_number;
		$this->hashAlgorithm = $hashAlgorithm;
	}

	/**
	 * @param string $master_key
	 * @return string
	 */
	public function encrypt($master_key='') {
		$bwpad=new bwPad($master_key, $this->hashAlgorithm);
		$pad=$bwpad->getPad(strlen($this->message), $this->keyNumber);
		$this->encrypted = $pad ^  $this->message;
		return $this->encrypted;
	}

	/**
     * Note use of encrypt to decrypt
	 * @param string $master_key
	 * @return string
	 */
	public function decrypt($master_key='') {
		if ($this->isHex($this->message)) $this->message=$this->hex2bin($this->message);
		return $this->encrypt($master_key);
	}

	/**
	 * @param $hexString
	 * @return bool
	 */
	private function isHex($hexString) {
		if (strlen($hexString) % 2 != 0) return false;
		if (preg_match('/^[0-9a-fA-F]*$/',$hexString)) return true;
		return false;
	}

	/**
	 * @param $hexString
	 * @return string
	 */
	private function hex2bin($hexString)
	{
		$hexLength=strlen($hexString);
		$binString='';
		for ($x = 1; $x <= $hexLength/2; $x++)
		{
			$binString .= chr(hexdec(substr($hexString,2 * $x - 2,2)));
		}
	
	return $binString;
	} 
}
