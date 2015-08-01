<?php

// tick use required as of PHP 4.3.0
declare(ticks = 1);

class bwEncFile {
	private $_filename;
	private $_tmp_filename;
	private $_contents;
	private $_fh;
	private $_key_number;
	private $_master_key;

	public function __construct($filename, $master_key='') {
		$this->_filename=$filename;
		$this->_tmp_filename=$filename.".tmp";
		$this->_master_key=$master_key;
		// setup signal handlers
		pcntl_signal(SIGTERM, array(&$this,"sig_handler"), false);
		pcntl_signal(SIGINT, array(&$this,"sig_handler"), false);
	}


	public function sig_handler($signo)
	{
		echo "Sig_handler called\n";
		switch ($signo) {
			case SIGTERM:
			case SIGINT:
	 			// handle shutdown tasks
				echo "Sig_handler called\n";
				unlink($this->_tmp_filename);
				exit;
			break;
			default:
			echo "Sig_handler called - no handler for $signo \n";
				// handle all other signals
     }
	}


	public function encrypt() {
		$this->_read();
		$this->_openWrite();
		$this->_encrypt();
		$this->_write();
		$this->_move();
	}

	public function decrypt() {
		$this->_read();
		$this->_openWrite();
		$this->_decrypt();
		$this->_write();
		$this->_move();
	}

	private function _write() {
		$len=fwrite($this->_fh, $this->_contents);
		if ($len != strlen($this->_contents)) throw new Exception("Problem writing to ". $this->_tmp_filename);
		fclose($this->_fh);
	}

	private function _decrypt() {
		$parts=preg_split('/,/',$this->_contents,2);
		$this->_key_number=$parts[0];
		$enc=new bwenc($parts[1],$this->_key_number);
		$this->_contents=$enc->decrypt($this->_master_key);
	}

	private function _encrypt() {
		$this->_key_number=time() % 131072;
		$enc=new bwenc($this->_contents,$this->_key_number);
		$this->_contents=$this->_key_number.','.$enc->encrypt($this->_master_key);
	}

	private function _read() {
		$this->_contents=file_get_contents($this->_filename);
		if ($this->_contents === false) throw new exception("Problem reading ". $this->_filename);
	}

	private function _openWrite() {
		$this->_fh=fopen($this->_tmp_filename,"w");
		if ($this->_fh === false) throw new exception("Problem opening ". $this->_tmp_filename. "for write");
	}

	private function _move() {
		system("mv ".$this->_tmp_filename." ".$this->_filename);
	}
}
