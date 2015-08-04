<?php

namespace backWall;

use backWall\HashAlgorithms\HashAlgorithmInterface;

/**
 * Class bwPad
 * @package backWall
 */
class bwPad {
	/** @var bwKeyGenerator */
	private $bwKeyGenerator;

    /** @var  HashAlgorithmInterface */
    private $hashAlgorithm;

	/**
	 * @param string $master_key
	 */
	public function __construct($master_key='', HashAlgorithmInterface $hashAlgorithm) {
        $this->hashAlgorithm = $hashAlgorithm;
		$this->bwKeyGenerator=new bwKeyGenerator($master_key, $hashAlgorithm);
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
			$this->bwKeyGenerator->generateNthKey($keyNumber);
			$pad.=$this->bwKeyGenerator->getBinary();
			$keyNumber++;
			$padSize+=$this->hashAlgorithm->getHashLength()/2; // Md5 raw length = 16 bytes
		}
		return $pad;
	}
}
