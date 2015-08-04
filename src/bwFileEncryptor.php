<?php

namespace backWall;

/**
 * Class bwFileEncryptor
 * @package backWall
 */
class bwFileEncryptor
{

    /** @var  String */
    private $filename;

    /** @var string */
    private $tmpFilename;

    /** @var  String */
    private $contents;

    /** @var  Resource */
    private $fh;

    /** @var  integer */
    private $keyNumber;

    /** @var string */
    private $masterKey;

    /**
     * @param $filename
     * @param string $masterKey
     */
    public function __construct($filename, $masterKey = '')
    {
        $this->filename = $filename;
        $this->tmpFilename = $filename . ".tmp";
        $this->masterKey = $masterKey;
        // setup signal handlers
        pcntl_signal(SIGTERM, array(&$this, "signalHandler"), false);
        pcntl_signal(SIGINT, array(&$this, "signalHandler"), false);
    }


    /**
     * @param integer $signo
     */
    public function signalHandler($signo)
    {
        switch ($signo) {
            case SIGTERM:
            case SIGINT:
                // handle shutdown tasks
                unlink($this->tmpFilename);
                exit;
                break;
            default:
                echo "signalHandler called - no handler for $signo \n";
                break;
        }
    }


    /**
     * @throws Exception
     */
    public function encrypt()
    {
        $this->getFileContents();
        $this->openOutputFileForWriting();
        $this->encryptContents();
        $this->writeContentsToFile();
        $this->overwriteOriginalFile();
    }

    /**
     * @throws Exception
     */
    public function decrypt()
    {
        $this->getFileContents();
        $this->openOutputFileForWriting();
        $this->decryptContents();
        $this->writeContentsToFile();
        $this->overwriteOriginalFile();
    }

    /**
     * @throws \RuntimeException
     */
    private function writeContentsToFile()
    {
        $len = fwrite($this->fh, $this->contents);
        if ($len != strlen($this->contents)) throw new \RuntimeException("Problem writing to " . $this->tmpFilename);
        fclose($this->fh);
    }

    /**
     *
     */
    private function decryptContents()
    {
        $parts = preg_split('/,/', $this->contents, 2);
        $this->keyNumber = $parts[0];
        $enc = new bwMessageEncryptor($parts[1], $this->keyNumber);
        $this->contents = $enc->decrypt($this->masterKey);
    }

    /**
     *
     */
    private function encryptContents()
    {
        $this->keyNumber = time() % 131072;
        $enc = new bwMessageEncryptor($this->contents, $this->keyNumber);
        $this->contents = $this->keyNumber . ',' . $enc->encrypt($this->masterKey);
    }

    /**
     * @throws \RuntimeException
     */
    private function getFileContents()
    {
        $this->contents = file_get_contents($this->filename);
        if ($this->contents === false) throw new \RuntimeException("Problem reading " . $this->filename);
    }

    /**
     * @throws \RuntimeException
     */
    private function openOutputFileForWriting()
    {
        $this->fh = fopen($this->tmpFilename, "w");
        if ($this->fh === false) throw new \RuntimeException("Problem opening " . $this->tmpFilename . "for write");
    }

    /**
     *
     */
    private function overwriteOriginalFile()
    {
        rename($this->tmpFilename, $this->filename);
    }
}
