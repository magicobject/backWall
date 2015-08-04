<?php

namespace backWall;

use backWall\HashAlgorithms\HashAlgorithmInterface;

/**
 * Class bwKeyGenerator
 * @package backWall
 */
class bwKeyGenerator
{

    /** @var  String the primary master key */
    private $primaryMasterKey;

    /** @var  String a master key */
    private $masterKey;

    private $computedValue;

    /** @var array Cache hash values to avoid multiple hashing of the same values */
    private $cache = [];

    /** @var  HashAlgorithmInterface */
    private $hashAlgorithm;

    /**
     * @param string $masterKey
     */
    public function __construct($masterKey = '', HashAlgorithmInterface $hashAlgorithm)
    {
        $this->hashAlgorithm = $hashAlgorithm;

        if (empty($masterKey)) {
            $this->primaryMasterKey = $this->hash("mA8fvL590dfbJ320'f#");
        } else {
            $this->primaryMasterKey = $this->hash($masterKey);
        }
    }

    private function reset()
    {
        $this->masterKey = $this->primaryMasterKey;
        $this->cache = [];
        $this->computedValue = $this->hash($this->masterKey);
    }


    /**
     * For super fast generation only deal with the lowest 8 bytes (recursively)
     * So for an 8 byte number there would be a max of 256*8 md5 ops not 256^8.
     *
     * @param int $n - the required key number
     * @param bool|true $reset
     * @return null - Use getBin() or getTxt() to get the actual computed key.
     */
    public function generateNthKey($n, $reset = true)
    {
        if ($reset) $this->reset();
        $again = false;

        $bigEnd = 0;
        $smallEnd = $n;

        if ($n >= 4) {
            $bigEnd = floor($n / 4);
            $smallEnd = $n % 4;
            $again = true;
        }

        for ($i = 0; $i <= $smallEnd; $i++) {
            $this->computedValue = $this->hash($this->computedValue ^ $this->masterKey);
        }

        if ($again) {
            $this->masterKey = $this->hash($this->computedValue ^ $this->masterKey); // Not without the hash - think about it
            // echo "$n : change master key to ".bin2hex($this->_master)."\n";
            $this->generateNthKey($bigEnd, false);
        }
    }

    /**
     * @return string
     */
    public function getText()
    {
        return bin2hex($this->computedValue);
    }

    /**
     * @return mixed
     */
    public function getBinary()
    {
        return $this->computedValue;
    }

    /**
     * @param $str
     * @return String
     */
    private function hash($str)
    {
        if (!isset($this->cache[$str])) {
            $hashValue = $this->hashAlgorithm->hash($str);
            $this->cache[$str] = $hashValue;
            return $hashValue;
        }
        return $this->cache[$str];
    }

}
