<?php


namespace backWall\HashAlgorithms;

/**
 * Class MD5HashAlgorithm
 * @package backWall\HashAlgorithms
 */
class MD5HashAlgorithm implements HashAlgorithmInterface
{
    /**
     * @param String $buffer
     * @return binary string
     */
    public function hash($buffer)
    {
        // Switch raw output on
        return md5($buffer, true);
    }

    /**
     * @return int
     */
    public function getHashLength()
    {
        return 32;
    }
}
