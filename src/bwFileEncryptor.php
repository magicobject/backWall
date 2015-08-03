<?php

namespace backWall;

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

    public function __construct($filename, $master_key = '')
    {
        $this->filename = $filename;
        $this->tmpFilename = $filename . ".tmp";
        $this->masterKey = $master_key;
        // setup signal handlers
        pcntl_signal(SIGTERM, array(&$this, "signalHandler"), false);
        pcntl_signal(SIGINT, array(&$this, "signalHandler"), false);
    }


    public function signalHandler($signo)
    {
        echo "Sig_handler called\n";
        switch ($signo) {
            case SIGTERM:
            case SIGINT:
                // handle shutdown tasks
                echo "Sig_handler called\n";
                unlink($this->tmpFilename);
                exit;
                break;
            default:
                echo "Sig_handler called - no handler for $signo \n";
                break;
        }
    }


    public function encrypt()
    {
        $this->_read();
        $this->_openWrite();
        $this->_encrypt();
        $this->_write();
        $this->_move();
    }

    public function decrypt()
    {
        $this->_read();
        $this->_openWrite();
        $this->_decrypt();
        $this->_write();
        $this->_move();
    }

    private function _write()
    {
        $len = fwrite($this->fh, $this->contents);
        if ($len != strlen($this->contents)) throw new Exception("Problem writing to " . $this->tmpFilename);
        fclose($this->fh);
    }

    private function _decrypt()
    {
        $parts = preg_split('/,/', $this->contents, 2);
        $this->keyNumber = $parts[0];
        $enc = new bwMessageEncryptor($parts[1], $this->keyNumber);
        $this->contents = $enc->decrypt($this->masterKey);
    }

    private function _encrypt()
    {
        $this->keyNumber = time() % 131072;
        $enc = new bwMessageEncryptor($this->contents, $this->keyNumber);
        $this->contents = $this->keyNumber . ',' . $enc->encrypt($this->masterKey);
    }

    private function _read()
    {
        $this->contents = file_get_contents($this->filename);
        if ($this->contents === false) throw new exception("Problem reading " . $this->filename);
    }

    private function _openWrite()
    {
        $this->fh = fopen($this->tmpFilename, "w");
        if ($this->fh === false) throw new exception("Problem opening " . $this->tmpFilename . "for write");
    }

    private function _move()
    {
        system("mv " . $this->tmpFilename . " " . $this->filename);
    }
}
