<?php
//
// Allow tests to be run with autoload debugging
//

//
// Set up session autoload caching
//
//require_once('framework/classes/Mode.php');
//require_once('framework/classes/Session.php');
//Session::sessionStart();
define('T0',microtime(true));
if (!defined('AUTOLOAD_PATH')) define('AUTOLOAD_PATH','.:lib');

if (!empty($argv[1]) && ('with-debug' == $argv[1])) {
	if (!defined('AUTOLOAD_DEBUG')) define('AUTOLOAD_DEBUG',1);
}

if (!function_exists('lcfirst')) {
	/**
	* Lower case the first letter of a string
	* Can be removed after php 5.3
	* @param string $string
	*/
	function lcfirst($string) {
		$string{0} = strtolower($string{0});
		return $string;
	}
}

if (!function_exists('uncamel_file_name')) {
/**
 * Convert a camel cased class name into a . delimited lower case name
 * so class SomeCamelCase would live in some.camel.case.php[5]
 * @param string $camel_string
 * @return string $file_string A . delimited string
 */
	function uncamel_class_name( $camel_string ) {
		$file_string=strtolower(preg_replace('/([a-z])([A-Z])/', '\1.\2', $camel_string));
		return($file_string);
	}
}


if (!function_exists('class_name_to_directory')) {
/**
 * Convert a class_name.php into class/name.php
 * @param string $class_name
 * @return string $path_string
 */
  function class_name_to_directory( $class_name ) {
    $file_string=preg_replace('/_/', '/', $class_name);
    return($file_string);
  }
}


if (!function_exists('__autoload')) {
	// '.' is implicit in the __autoload() function
	if (!defined('AUTOLOAD_PATH')) {
		if (!empty($_ENV['USER'])) $username=$_ENV['USER'];
		if (!empty($_COOKIE['username'])) $username=$_COOKIE['username'];
	}
	/**
	* Automatically load class files
	* @param string $class_name The class name to load.
	*/
	function __autoload($class_name) {

		if (!empty($_SESSION) && !empty($_SESSION['autoload_cache'][$class_name])) {
			if (defined('AUTOLOAD_DEBUG')) echo __FUNCTION__." Cache hit $class_name <br/>\n";
			include($_SESSION['autoload_cache'][$class_name]);
			return;
		}


		$directories=explode(':',AUTOLOAD_PATH);

		// Add in the cwd if necessary at the end  of the autoload path
		$have_cwd=false;
		foreach($directories as $name=>$value) {
			if ('.' == $value) $have_cwd=true;
		}
		if (!$have_cwd) $directories[]='.';

		if (autoload_try_class_name($directories,$class_name,$class_name)) return;

		$file_name=class_name_to_directory($class_name);
		if (autoload_try_class_name($directories,$file_name,$class_name)) return;

		$lc_class_name=strtolower($class_name);
		if (autoload_try_class_name($directories,$lc_class_name,$class_name)) return;

//		$class_name=uncamel_class_name($class_name);
//		if (autoload_try_class_name($directories,$class_name,$class_name)) return;

		trigger_error('Autoloader could not find .php class file for class '.$class_name);
	}
}

if (!function_exists('autoload_try_class_name')) {
	/**
	*
	* Try to find the class_name in the AUTOLOAD_PATH
	* then in the cwd.
	*
	* @param arrary<String> $directories List of directories in the AUTOLOAD_PATH
	* @param string $part_file_name
	* @param string $required_class_name
	* @return Boolean found
	*/
	function autoload_try_class_name($directories,$part_file_name,$required_class_name) {

		if (defined('AUTOLOAD_DEBUG')) echo __FUNCTION__." Trying $part_file_name <br/>\n";

		$phpExtensions=array('php'); // Add in php4, php5, php6 as required

		foreach($directories as $directory) {
			foreach($phpExtensions as $php) { // Add php6 when necessary
				if (autoload_directory_exists($directory)) {
					$file=$directory.'/'.$part_file_name.'.'.$php;
					if (autoload_try_file($file,$required_class_name)) return true;
				}
			}
		}
		return false;
	}
}

if (!function_exists('autoload_try_file')) {
/**
 * Try to load a possible class file. Check that the class exists.
 * if it does return true.
 *
 * @param string $filename
 * @param string $required_class_name
 */
	function autoload_try_file($file_name,$required_class_name) {
		if (defined('AUTOLOAD_DEBUG')) echo __FUNCTION__." Trying $file_name <br/>\n";
		if (file_exists($file_name)) {
		if (defined('AUTOLOAD_DEBUG')) echo __FUNCTION__." File hit so looking for $required_class_name in $file_name <br/>\n";
			include_once($file_name);
			if (class_exists($required_class_name)|| interface_exists($required_class_name)) {
				if (defined('AUTOLOAD_DEBUG')) echo __FUNCTION__." Class HIT $required_class_name found in $file_name <br/>\n";
				$_SESSION['autoload_cache'][$required_class_name]=$file_name;
				return true;
			} else {
				if (defined('AUTOLOAD_DEBUG')) echo __FUNCTION__." $required_class_name NOT found in $file_name <br/>\n";
			}
		} else {
				if (defined('AUTOLOAD_DEBUG')) echo __FUNCTION__." no such file $file_name <br/>\n";
		}
		return false;
	}
}

if (!function_exists('autoload_directory_exists')) {
	/**
	* @param $directory - directory to check
	* @return boolean
	*/
	function autoload_directory_exists( $directory ) {
		static $good_directories_cache=array();
		static $bad_directories_cache=array();

		if (!empty($good_directories_cache[$directory])) return true;
		if (!empty($bad_directories_cache[$directory])) return true;

		if (is_dir($directory)) {
			$good_directories_cache[$directory]=1;
			return true;
		} else {
			$bad_directories_cache[$directory]=1;
			return false;
		}
	}
}

