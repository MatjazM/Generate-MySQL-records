<?php

/**
 * Unit testing file for DataGenerator.class.php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Matjaž Mrgole
 */

require_once 'tests/src/autoload.php';
require_once 'tests/src/common.php';

class DataGeneratorTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var DataGenerator object
	 */
	private $dataGenerator;

	/**
	 * Creates DataGenerator object
	 *
	 * @return void
	 */
	protected function setUp() {
		global $columns, $indexes;

		$this->dataGenerator = new DataGenerator($columns, $indexes, new ConfigUnitTest());
	}

	/**
	 * Destroys DataGenerator object
	 *
	 * @returns void
	 */
	protected function tearDown() {
		unset($this->dataGenerator);
	}

	/**
	 * Tests if generateData method throws exception when table doesn't exits
	 *
	 * @expectedException RunException
	 * @return void
	 */
	public function testGenerateDataException() {
		$this->dataGenerator->generateData('iDontExists', 1);
	}

	/**
	 * Test generateData function
	 *
	 * @return void
	 */
	public function testGenerateData() {
		$generated1 = $this->dataGenerator->generateData('testTable1', 1);
		$generated2 = $this->dataGenerator->generateData('testTable2', 2);
		$generated3 = $this->dataGenerator->generateData('testTable3', 11);
		$generated4 = $this->dataGenerator->generateData('testTable4', 1);

		// simple generation test
		$this->assertStringMatchesFormat('%s', $generated1['name'][0]);
		$this->assertEmpty($generated1['id']);
		$this->assertEmpty($generated1['name'][1]); // only one record

		// test change of $length in combination with unique index
		$hashTable = array();
		$success = true;
		for ($i = 0; $i < 11; $i++) {
			if (isset($hashTable[$generated3['id'][$i]])) {
				$success = false;
				break;
			}

			$hashTable[$generated3['id'][$i]] = true;
		}
		$this->assertTrue($success);

		// test two records
		$this->assertStringMatchesFormat('%d', (string) $generated2['id'][0]);
		$this->assertStringMatchesFormat('%s', $generated2['name'][0]);
		$this->assertStringMatchesFormat('%d', (string) $generated2['id'][1]);
		$this->assertStringMatchesFormat('%s', $generated2['name'][1]);
		$this->assertEmpty($generated2['id'][2]);
		$this->assertEmpty($generated2['name'][2]);

		// test all possibilities
		$this->assertStringMatchesFormat('%d', (string) $generated4['id1'][0]);
		$this->assertEquals(10, $generated4['id2'][0]); // foreign key
		$this->assertStringMatchesFormat('%d', (string) $generated4['id3'][0]);
		$this->assertStringMatchesFormat('%d', (string) $generated4['id4'][0]);
		$this->assertStringMatchesFormat('%d', (string) $generated4['id5'][0]);
		$this->assertStringMatchesFormat('%f', str_replace('\'', '', $generated4['f1'][0]));
		$this->assertStringMatchesFormat('%f', str_replace('\'', '', $generated4['f2'][0]));
		$this->assertStringMatchesFormat('%f', str_replace('\'', '', $generated4['f3'][0]));
		$this->assertStringMatchesFormat('%f', str_replace('\'', '', $generated4['f4'][0]));
		$this->assertStringMatchesFormat('%s', $generated4['s1'][0]);
		$this->assertStringMatchesFormat('%s', $generated4['s2'][0]);
		$this->assertStringMatchesFormat('%s', $generated4['s3'][0]);
		$this->assertStringMatchesFormat('%s', $generated4['s4'][0]);
		$this->assertStringMatchesFormat('%s', $generated4['s5'][0]);
		$this->assertStringMatchesFormat('%s', $generated4['s6'][0]);
		$this->assertStringMatchesFormat('%s', $generated4['s7'][0]);
		$this->assertStringMatchesFormat('%s', $generated4['s8'][0]);
		$this->assertStringMatchesFormat('%s', $generated4['s9'][0]);
		$this->assertStringMatchesFormat('%s', $generated4['s10'][0]);
		$this->assertStringMatchesFormat('%s', $generated4['d1'][0]);
		$this->assertTrue(strlen($generated4['d1'][0]) == 12);
		$this->assertStringMatchesFormat('%s', $generated4['d2'][0]);
		$this->assertTrue(strlen($generated4['d2'][0]) == 21);
		$this->assertStringMatchesFormat('%s', $generated4['d3'][0]);
		$this->assertTrue(strlen($generated4['d3'][0]) == 10);
		$this->assertStringMatchesFormat('%s', $generated4['d4'][0]);
		$this->assertTrue(strlen($generated4['d4'][0]) == 6);
		$this->assertStringMatchesFormat('%s', $generated4['o1'][0]);
		$this->assertStringMatchesFormat('%s', $generated4['o2'][0]);
		$this->assertStringMatchesFormat('%i', str_replace(array('\'', 'b', ' '), '', strlen($generated4['o3'][0]) == 9));
		$this->assertTrue(strlen($generated4['o3'][0]) == 9);
		$this->assertEmpty($generated1['id1'][1]); // only one record
	}
}