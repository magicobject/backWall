<?php

namespace backWall;

class bwPad {
	/** @var bwKeyGenerator */
	private $bwKeyGenerator;

	/**
	 * @param string $master_key
	 */
	public function __construct($master_key='') {
		$this->bwKeyGenerator=new bwKeyGenerator($master_key);
	}

	/**
	 * @param $size
	 * @param $initialKeyNumber
	 * @return string
	 */
	public function getPad( $size, $initialKeyNumber ) {
		$pad="";
		$padSize=0;
		$keyNumber=$initialKeyNumber;

		// Loop forward getting bw pad stripes as this is the most efficient way.
		while(strlen($padSize < $size)) {
			$this->bwKeyGenerator->nth($keyNumber);
			$pad.=$this->bwKeyGenerator->getBin();
			$keyNumber++;
			$padSize+=16; // Md5 raw length = 16 bytes
		}
		return $pad;
	}
}
