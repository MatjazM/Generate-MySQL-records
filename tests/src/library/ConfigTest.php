<?php

/**
 * Unit testing file for Config.class.php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Matjaž Mrgole
 */

require_once 'tests/src/autoload.php';

class ConfigTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Config object
	 */
	private $config;

	/**
	 * Creates Config object
	 *
	 * @return void
	 */
	protected function setUp() {
		$this->config = new Config(array('testInitValue' => 1));
	}

	/**
	 * Destroys Config object
	 *
	 * @returns void
	 */
	protected function tearDown() {
		unset($this->config);
	}

	/**
	 * Tests set function
	 *
	 * @return void
	 */
	public function testSetAndGet() {
		$this->config->set('testKey', 'testValue');
		$actual1 = $this->config->testKey;
		$expected1 = 'testValue';

		// Test __get
		$actual2 = $this->config->__get('testInitValue');
		$expected2 = 1;

		$this->assertEquals($expected1, $actual1);
		$this->assertEquals($expected2, $actual2);
	}

	/**
	 * Getting key that doesn't exists
	 *
	 * @expectedException RunException
	 * @return void
	 */
	public function testGetException() {
		$this->config->iDontExists;
	}

	/**
	 * Setting the same value twice
	 *
	 * @expectedException RunException
	 * @return void
	 */
	public function testSetException() {
		$this->config->set('testKey', 'testValue');
		$this->config->set('testKey', 'testValue');
	}
}