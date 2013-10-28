<?php

/**
 * Unit testing file for Benchmark.class.php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Matjaž Mrgole
 */

require_once 'tests/src/autoload.php';

class BenchmarkTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Benchmark object
	 */
	private $benchmark;

	/**
	 * Creates Benchmark object
	 *
	 * @return void
	 */
	protected function setUp() {
		$this->benchmark = new Benchmark();
	}

	/**
	 * Destroys Benchmark object
	 *
	 * @returns void
	 */
	protected function tearDown() {
		unset($this->benchmark);
	}

	/**
	 * Calling endTime() before startTime()
	 *
	 * @expectedException RunException
	 * @return void
	 */
	public function testEndTimeException() {
		$this->benchmark->endTime();
	}

	/**
	 * Calling startTime() after startTime()
	 *
	 * @expectedException RunException
	 * @return void
	 */
	public function testStartTimeException() {
		$this->benchmark->startTime();
		$this->benchmark->startTime();
	}

	 /**
	 * Calling startTime() after startTime()
	 *
	 * @expectedException RunException
	 * @return void
	 */
	public function testShowTimeException() {
		$this->benchmark->showTime('', 0);
	}

	/**
	 * Test showTime method
	 *
	 * @return void
	 */
	public function testShowTime() {
		$this->benchmark->startTime('testGroup');
		$this->benchmark->endTime('testGroup');

		$this->benchmark->startTime('testGroup');
		$this->benchmark->endTime('testGroup');

		$compare1 = $this->benchmark->showTime('testGroup', 0);
		$compare2 = $this->benchmark->showTime('testGroup', 0, 2);

		$this->assertStringMatchesFormat('%d', $compare1);
		$this->assertStringMatchesFormat('%f', $compare2);
	}
}