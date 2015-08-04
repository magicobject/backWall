<?php


namespace backWall\HashAlgorithms;

/**
 * Class MD5HashAlgorithm
 * @package backWall\HashAlgorithms
 */
class SHA256HashAlgorithm implements HashAlgorithmInterface
{
    /**
     * @param String $buffer
     * @return binary string
     */
    public function hash($buffer)
    {
        // Switch raw output on
        return hash('sha256', $buffer, true);
    }

    /**
     * @return int
     */
    public function getHashLength()
    {
        return 64;
    }
}
