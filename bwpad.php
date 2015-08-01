<?php

class bwpad {
	private $_bw;

	public function __construct($master_key='') {
		$this->_bw=new bwkey($master_key);
	}

	public function getPad( $size, $start_key_number ) {
		$pad="";
		$pad_size=0;
		$key_number=$start_key_number;
		// Loop forward getting bw pad stripes as this is the most efficient way.
		while(strlen($pad_size < $size)) {
			$this->_bw->nth($key_number);
			$pad.=$this->_bw->getBin();
			$key_number++;
			$pad_size+=16; // Md5 raw length = 16 bytes
		}
		return $pad;
	}
}
