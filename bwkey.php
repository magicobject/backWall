<?php

class bwkey {

	private $_primary_master;
	private $_master;
	private $_value;
	private $_cache;

	public function __construct($master='') {
		if (empty($master)) {
			$this->_primary_master=$this->_md5("mA8fvL590dfbJ320'f#");
		} else {
			$this->_primary_master=$this->_md5($master);
		}
	}

	private function _reset() {
		$this->_master=$this->_primary_master;
		$this->_value=$this->_md5($this->_master);
		$this->_cache=array();
	}

	// For super fast generation only deal with the lowest 8 bytes (recursively)
	// So for an 8 byte number there would be a max of 256*8 md5 ops not 256^8.
	public function nth($n, $reset=true) {
		if ($reset) $this->_reset();
		$again=false;

		$big_end=0;
		$small_end=$n;

		if ($n > 3) {
			$big_end=floor($n/4);
			$small_end=$n % 4;
			$again=true;
		}

		for ($i=0; $i <= $small_end; $i++) {
			$this->_value=$this->_md5($this->_value ^ $this->_master,true);
		}

		if ($again) {
			$this->_master=$this->_md5($this->_value ^ $this->_master, true); // Not without the MD5 - think about it
			// echo "$n : change master key to ".bin2hex($this->_master)."\n";
			$this->nth($big_end, false);
		}
	}

	public function getTxt() {
		return bin2hex( $this->_value );
	}

	public function getBin() {
		return $this->_value;
	}

	// md5 binary cache
	private function _md5($str) {
		if (!isset($this->_cache[$str])) $this->_cache[$str]=md5($str,true);
		return $this->_cache[$str];
	}

}
