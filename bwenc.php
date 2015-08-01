<?php

class bwenc {
	private $_message;
	private $_key_number;
	private $_encrypted;

	public function __construct($msg, $key_number) {
		$this->_message=$msg;
		$this->_key_number=$key_number;
	}

	public function encrypt($master_key='') {
		$bwpad=new bwpad($master_key);
		$pad=$bwpad->getPad(strlen($this->_message), $this->_key_number);
		$this->encrypted='';
		$len=strlen($this->_message);
		$this->_encrypted .= $pad ^  $this->_message;
		return $this->_encrypted;
	}

	public function decrypt($master_key='') {
		if ($this->_isHex($this->_message)) $this->_message=$this->_hex2bin($this->_message);
		return $this->encrypt($master_key);
	}

	private function _isHex($hexString) {
		if (strlen($hexString) % 2 != 0) return false;
		if (preg_match('/^[0-9a-fA-F]*$/',$hexString)) return true;
		return false;
	}

	private function _hex2bin($hexString)
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
