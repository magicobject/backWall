<?php


namespace backWall\HashAlgorithms;


interface HashAlgorithmInterface
{
    /**
     * @param String $buffer
     * @return String
     */
    public function hash($buffer);

    /**
     * @return integer
     */
    public function getHashLength();
}