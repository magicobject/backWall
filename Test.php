<?php
/**
 * Test
 *
 * Set up appropriate test environment for testing
 * This is a replacement for PHPUnit and can be used in almost exactly the same way
 * except that you just override and instantiate the Test class and you can call
 * it using php not phpunit.
 *
 * There are some advantages over PHPUnit for example environment independence and
 * the fact that this automatically reports on the test methods it runs. In all other
 * ways it is pretty much exactly the same when run form the command line.
 *
 * You don't even need to provide a constructor in your test class as the constructor
 * in Test will be called automatically.
 *
 * To add new types of assertion just follow the lead from the protected 'assert'
 * funcitons in this file. Note the way the private _assert method is called. It
 * is done this wat so that the debug_traceback has the calling funtion details
 * in a predictable place.
 *
 * As with PHPUnit you can add any functions in your test class but only ones with
 * names starting with 'test' will be run automatically.
 */

//***** BASIC SETUP *****
/**
 * Provide an error handler to promote errors to exceptions which can then be tested
 * TODO: Upate to use standard error handler
 */
function testing_error_handler($errno,$msg) {

	// Create full back trace
	$stackTrace = '';
	$traceArray = debug_backtrace();
	$numTraceLines = count($traceArray);
	for($i=1; $i<$numTraceLines; $i++) {
		if (isset($traceArray[$i]['file'])) $stackTrace .= $traceArray[$i]['file'].' : ';
		if (isset($traceArray[$i]['line'])) $stackTrace .= $traceArray[$i]['line'].' : ';
		if (isset($traceArray[$i]['class'])) $stackTrace .= $traceArray[$i]['class'].'->';
		if (isset($traceArray[$i]['function'])) $stackTrace .= $traceArray[$i]['function'].'()';
		if (isset($traceArray[$i]['args'])) {
			$stackTrace .= ', Arguments:';
			foreach($traceArray[$i]['args'] as $arg) {
				if (is_object($arg)) {
					$stackTrace .= ' '.print_r($arg,true).',';
				} else {
					$stackTrace .= ' '.$arg.',';
				}
			}
			rtrim($stackTrace,',');
		}
		$stackTrace .= "\n";
	}

	// Throw exception with back trace included in message
	/*
	if (!empty($_SESSION)) {
		echo "\n=========== session ===========\n";
		print_r($_SESSION);
		echo "=========== /session ==========\n";
	}
	*/
	throw new Exception("Test:ERROR $errno - $msg\nSTACK TRACE: ".$stackTrace);
	exit;
}
set_error_handler('testing_error_handler');

$_COOKIE['name'] = 'Testing'; // Default for SQL logging

error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('UTC'); // to deal with pedanticness in E_STRICT and date() calls.



class Test {

	private $assertion_count = 0;
	private $assertion_fail_count = 0;
	private $test_method_count = 0;

	private $test_method_name='';

	private $expected='';
	private $actual='';

	private $total_time=0.0;

	// A comment to be added to assert failure output
	private $comment='';

	/**
	* Run the testst
	*/
	public function __construct() {
		try {
			$this->runTests();
		} catch (Exception $exc) {
			echo "Testing failed: Exception message follows:\n".$exc->getMessage()."\n";
		}
		// if ($this->assertion_fail_count > 0) exit(1);
		exit($this->assertion_fail_count);
	}

	/**
	* Loop over methods in the derived class lookig for ones starting with 'test'.
	* Run them.
	*/
	private function runTests() {
		$test_methods=get_class_methods($this);
		$this->setup();
		if (!empty($_ARGV[2])) {
			$test_method=$_ARGV[2];
			$this->runTestMethod($test_method);
		} else {
			foreach($test_methods as $test_method) {
				if (preg_match('/^test/',$test_method)) {
					$this->runTestMethod($test_method);
				}
			}
		}
		$this->printResultsSummary();
		$this->teardown();
	}

	/**
	* run an individual test method
	*/
	private function runTestMethod($test_method) {
			$this->beforeTest();
				$this->test_method_name=$test_method;
				echo "===== $test_method "; flush();
				$t0 = microtime(true);
				$this->$test_method();
				$t1 = microtime(true);
				$delta=number_format($t1 - $t0,5);
				echo "complete in $delta s =====\n";
				$this->total_time+=$delta;
				$this->test_method_count++;
			$this->afterTest();
	}
	/**
	* Boolean Assertion
	* Must be private. Call via assertTrue or assertFalse
	*/
	private function assert( $bool ) {
		return $this->_assert($bool);
	}
	/**
	*
	*/
	protected function assertTrue( $bool, $comment='' ) {
		$this->comment=$comment;
		$this->set_expected_actual('true','false');
		return $this->_assert($bool);
	}
	/**
	*
	*/
	protected function assertFalse( $bool, $comment='' ) {
		$this->comment=$comment;
		$this->set_expected_actual('false','true');
		return $this->_assert(!$bool);
	}

	/**
	* Private assertion function to print out details if the assertion fails
	* @param boolean $bool
	*/
	private function _assert( $bool ) {
		$this->assertion_count++;
		if	(!$bool) {
			$this->assertion_fail_count++;
			$backtrace=debug_backtrace();
			$file=preg_replace('/^.*\//','',$backtrace[1]['file']);
			$line=$backtrace[1]['line'];
			if (!empty($this->comment)) echo $this->comment.' : ';
			echo $file.' '.$this->test_method_name." Assertion failed at line $line.
Expected: '".$this->expected.'\'
		 Got: \''.$this->actual.'\''."\n";
		}
		return $bool;
	}

	protected function assertDifferent( $expected, $actual, $comment='' ) {
		$this->comment=$comment;
		$this->set_expected_actual($expected,$actual);
		return $this->_assert($expected != $actual);
	}

	/**
	* Don't share code between assertEqual and assertEquals as this messes
	* up the backtrace when the assertions fail.
	*/
	protected function assertEqual( $expected, $actual, $comment='' ) {
		$this->comment=$comment;
		$this->set_expected_actual($expected,$actual);
		return $this->_assert($expected == $actual);
	}
	/**
	*
	*/
	protected function assertMatches( $expected, $actual, $comment='' ) {
		$this->comment=$comment;
		$this->set_expected_actual($expected,$actual);
		return $this->_assert(preg_match("/$expected/",$actual));
	}

	/**
	* Match floating point numbers
	*/
	protected function assertClose( $expected, $actual, $comment='' ) {
		$this->comment=$comment;
		$this->set_expected_actual($expected,$actual);
		return $this->_assert(abs($expected - $actual) < 0.00000001);
	}
	/**
	*
	*/
	protected function assertEquals( $expected, $actual, $comment='' ) {
		$this->comment=$comment;
		$this->set_expected_actual($expected,$actual);
		return $this->_assert($expected == $actual);
	}
	/**
	*
	*/
	protected function assertArraysEqual($expected, $actual, $comment='') {
		$this->assertIsArray($expected,'Check that expected value in assertArraysEqual is an array');
		$this->assertIsArray($actual,'Check that actual value in assertArraysEqual is an array');
		$this->comment=$comment;
		$this->set_expected_actual(print_r($expected,true),print_r($actual,true));
		$diff=array_diff($expected,$actual);
		return $this->_assert(empty($diff));
	}
	/**
	* Same as assertEqual(s) but spaces are stripped out
	* before the comparison.
	*/
	protected function assertEqualsIgnoreSpaces( $expected, $actual, $comment='' ) {
		$this->comment=$comment;
		$this->set_expected_actual($expected,$actual);
		$ex=preg_replace('/ /','',$expected);
		$ac=preg_replace('/ /','',$actual);
		return $this->_assert($ex == $ac);
	}
	/**
	* Same as assertEqual(s) but case differences are igniored
	*/
	protected function assertEqualsIgnoreCase( $expected, $actual, $comment='' ) {
		$this->comment=$comment;
		$this->set_expected_actual($expected,$actual);
		$ex=strtolower($expected);
		$ac=strtolower($actual);
		return $this->_assert($ex == $ac);
	}

	/**
	*
	*/
	protected function assertMatrixEqual(Matrix $m1, Matrix $m2, $comment) {
		$expected=$m1->getData();
		$actual=$m2->getData();
		$this->comment=$comment;
		$this->set_expected_actual($expected,$actual);

		$col1=$m1->getColumns();
		$col2=$m2->getColumns();
		$row1=$m1->getRows();
		$row2=$m2->getRows();

		if (!$this->_assert($col1 == $col2)) return false;
		if (!$this->_assert($row1 == $row2)) return false;

		for ($i=0; $i < $row1; $i++) {
			for ($j=0; $j < $row1; $j++) {
				$this->assert(abs($m1->getElement($i,$j) - $m2->getElement($i,$j)) < 0.000001);
			}
		}
		return true;
	}
	/**
	*
	*/
	protected function assertGreaterThan( $expected, $actual, $comment='' ) {
		$this->comment=$comment;
		$this->set_expected_actual($expected,$actual);
		return $this->_assert($expected > $actual);
	}

	/**
	*
	*/
	protected function assertLessThan( $expected, $actual, $comment='' ) {
		$this->comment=$comment;
		$this->set_expected_actual($expected,$actual);
		return $this->_assert($expected < $actual);
	}

	/**
	*
	*/
	protected function assertIsNull( $expected, $comment='' ) {
		$this->comment=$comment;
		$type=gettype($expected);
		$this->set_expected_actual('NULL',$type);
		return $this->_assert('NULL' == $type);
	}

	/**
	*
	*/
	protected function assertIsString( $expected, $comment='' ) {
		$this->comment=$comment;
		$type=gettype($expected);
		$this->set_expected_actual('string',$type);
		return $this->_assert('string' == $type);
	}

	/**
	*
	*/
	protected function assertIsObject( $expected, $comment='' ) {
		$this->comment=$comment;
		$type=gettype($expected);
		$this->set_expected_actual('object',$type);
		return $this->_assert('object' == $type);
	}
	/**
	*
	*/
	protected function assertIsBoolean( $expected, $comment='' ) {
		$this->comment=$comment;
		$type=gettype($expected);
		$this->set_expected_actual('boolean',$type);
		return $this->_assert('boolean' == $type);
	}
	/**
	*
	*/
	protected function assertIsInteger( $expected, $comment='' ) {
		$this->comment=$comment;
		$type=gettype($expected);
		$this->set_expected_actual('integer',$type);
		return $this->_assert('integer' == $type);
	}
	/**
	*
	*/
	protected function assertIsDouble( $expected, $comment='' ) {
		$this->comment=$comment;
		$type=gettype($expected);
		$this->set_expected_actual('double',$type);
		return $this->_assert('double' == $type);
	}
	/**
	*
	*/
	protected function assertIsFloat( $expected, $comment='' ) {
		$this->comment=$comment;
		$type=gettype($expected);
		$this->set_expected_actual('double',$type); // floats are type double
		return $this->_assert('double' == $type);
	}
	/**
	*
	*/
	protected function assertIsArray( $expected, $comment='' ) {
		$this->comment=$comment;
		$type=gettype($expected);
		$this->set_expected_actual('array',$type);
		return $this->_assert('array' == $type);
	}
	/**
	*
	*/
	protected function assertIsResource( $expected, $comment='' ) {
		$this->comment=$comment;
		$type=gettype($expected);
		$this->set_expected_actual('resource',$type);
		return $this->_assert('resource' == $type);
	}
	/**
	*
	*/
	protected function assertClass( $expected, $instance, $comment='' ) {
		$this->comment=$comment;
		$type=get_class($instance);
		$this->set_expected_actual($expected,$type);
		return $this->_assert($expected == $type);
	}
	/**
	*
	*/
	protected function assertInstanceOf( $expected, $instance, $comment='' ) {
		$this->comment=$comment;
		$type=get_class($instance);
		$this->set_expected_actual($expected,$instance);
		return $this->_assert($instance instanceof $expected);
	}
	/**
	* Check the passed parameter is an Exception or a subclass of Exception
	*/
	protected function assertException( $ex, $comment='' ) {
		$this->comment=$comment;
		$type=get_class($ex);
		$this->set_expected_actual('Subclass of Exception',$type);
		return $this->_assert('Exception' == $type || is_subclass_of($ex,'Exception'));
	}
	/**
	*
	*/
	protected function assertExceptionMessage( $expected_message, Exception $exception, $comment='' ) {
		$this->comment=$comment;
		$message_from_exception=$exception->getMessage();
		$this->set_expected_actual($expected_message,$message_from_exception);
		return $this->_assert($expected_message == $message_from_exception);
	}
	/**
	*
	*/
	protected function assertExceptionMessageStartsWith( $expected_message, Exception $exception, $comment='' ) {
		$this->comment=$comment;
		$message_from_exception=$exception->getMessage();
		$this->set_expected_actual("$expected_message ...",$message_from_exception);
		return $this->_assert(strncmp($message_from_exception,$expected_message,strlen($expected_message)) == 0);
	}
	/**
	*
	*/
	protected function assertExceptionMessageMatches( $match, Exception $exception, $comment='' ) {
		$this->comment=$comment;
		$message_from_exception=$exception->getMessage();
		$this->set_expected_actual("... $match ...",$message_from_exception);
		return $this->_assert(preg_match("/$match/",$message_from_exception) > 0);
	}
	/**
	*
	*/
	protected function assertRegexp( $regexp, $match, $comment='' ) {
		$this->comment=$comment;
		$this->set_expected_actual("Matches $regexp",$match);
		return $this->_assert(preg_match($regexp,$match));
	}
	/**
	*
	*/
	protected function assertResource( $resource, $comment='' ) {
		$this->comment=$comment;
		return $this->_assert(is_resource($resource));
	}
	/**
	*
	*/
	protected function assertScalar( $scalar, $comment='' ) {
		$this->comment=$comment;
		return $this->_assert(is_scalar($scalar));
	}
	/**
	*
	*/
	protected function assertNumeric( $numeric, $comment='' ) {
		$this->comment=$comment;
		return $this->_assert(is_numeric($numeric));
	}
	/**
	/**
	* Store the expected and actual values for later processing by _assert()
	* @param mixed $expected
	* @param mixed $actual
	*/
	private function set_expected_actual($expected, $actual) {
		$this->expected=$expected;
		$this->actual=$actual;
	}

	/**
	*
	*/
	private function printResultsSummary() {
		echo $this->test_method_count." tests were run\n";
		echo $this->assertion_count." assertions were run\n";
		echo $this->assertion_fail_count." assertions failed\n";
		echo 'Total time for tests '.$this->total_time." s\n";
	}

	/**
	* Force an assertion failure
	*/
 	public function fail($message) {
		echo "ERROR: $message\n";
		$this->assertion_fail_count++;
	}

/**
 * overridable function run before all tests are started
 * Useful for example, to set up an initial db connection
 */
	protected function setup() {
	}
/**
 * overridable function run before after all tests have been run
 */
	protected function teardown() {
		$delta=microtime(true) - T0;
		echo "Complete in $delta"."s\n";
	}
/**
 * overridable function run before each test method is called
 */
	protected function beforeTest() {
	}
/**
 * overridable function run after each test method is called
 */
	protected function afterTest() {
	}

/**
 * Pull in $fileNname (for testing)
 * pass back the output 
 */
	public function loadFile($fileName) {
		ob_start();
		require $fileName;
		return ob_get_clean();
	}

/**
 * Pull in $fileName (for testing)
 * pass back the template for controller non OO testing 
 * $fileName must instantiate a Template called $tpl
 */
	public function loadFileReturnTemplate($fileName) {
		ob_start();
		require $fileName;
		$html=ob_get_clean();
		return $tpl;
	}

/**
 * Set up Mimic Ajax mimicry
 */
	protected function mimicAjax() {
		if (empty($_SERVER)) $_SERVER=array();
    $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
	}

/**
 * Reload / reset data to a sane state
 */
	protected function resetData($db_name='') {
		system("sql $db_name < ../sql/test_data.sql");
	}
}
