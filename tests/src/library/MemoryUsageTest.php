<?php

/**
 * Unit testing file for MemoryUsage.class.php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Matjaž Mrgole
 */

require_once 'tests/src/autoload.php';
require_once 'tests/src/common.php';

class MemoryUsageTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var MemoryUsage object
	 */
	private $memoryUsage;

	/**
	 * Creates MemoryUsage object
	 *
	 * @return void
	 */
	protected function setUp() {
		global $columns;

		$this->memoryUsage = new MemoryUsage(new ConfigUnitTest(), $columns);
	}

	/**
	 * Destroys MemoryUsage object
	 *
	 * @returns void
	 */
	protected function tearDown() {
		unset($this->memoryUsage);
	}

	/**
	 * Tests if calculateMaxMemoryUsage method throws exception when table doesn't exits
	 *
	 * @expectedException RunException
	 * @return void
	 */
	public function testCalculateMaxMemoryUsageException() {
		$this->memoryUsage->calculateMaxMemoryUsage('iDontExists', 1);
	}

	/**
	 * Test generateData function
	 *
	 * @return void
	 */
	public function testCalculateMaxMemoryUsage() {
		$generated1 = $this->memoryUsage->calculateMaxMemoryUsage('testTable1', 1);
		$generated2 = $this->memoryUsage->calculateMaxMemoryUsage('testTable2', 2);
		$generated3 = $this->memoryUsage->calculateMaxMemoryUsage('testTable3', 11);
		$generated4 = $this->memoryUsage->calculateMaxMemoryUsage('testTable4', 1);

		$this->assertEquals($generated1, 88);
		$this->assertEquals($generated2, 272);
		$this->assertEquals($generated3, 528);
		$this->assertEquals($generated4, 2064);
	}
}