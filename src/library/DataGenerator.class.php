<?php

/**
 * DataGenerator for generating random records
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Matjaž Mrgole
 */

class DataGenerator {

	/**
	 * Stores data about database tables
	 * @var array
	 */
	private $columns = array();
	/**
	 * Stores data about database indexes inside tables
	 * @var array
	 */
	private $indexes = array();
	/**
	 * @var Config object
	 */
	private $config;

	/**
	 * @param array $databaseColumns
	 * @param array $databaseIndexes
	 * @param ConfigInterface $config
	 * @return void
	 */
	public function __construct(array $databaseColumns, array $databaseIndexes, ConfigInterface $config) {
		$this->columns = $databaseColumns;
		$this->indexes = $databaseIndexes;
		$this->config = $config;
	}

	/**
	 * Generates $noOfRecords rows for $table
	 *
	 * @throws RunException
	 * @param string $table
	 * @param integer $noOfRecords
	 * @return array
	 */
	public function generateData($table, $noOfRecords) {
		$returnArray = array();

		if (isset($this->columns[$table])) {
			foreach ($this->columns[$table] as $key => $value) {
				$unique = false;
				if (isset($this->indexes[$table])) {
					foreach ($this->indexes[$table] as $index) { // check if we need unique value
						if (in_array($key, $index['indexes'])) {
							if ($index['unique'] === true) {
								$unique = true;
								break;
							}
						}
					}
				}

				if (isset($value[2])) { // foreign constrains
					$reference = $value[2];
				} else {
					$reference = null;
				}

				// integers
				if (strpos($value[0], 'tinyint') !== false && $value[1] != 'auto_increment') {
					$returnArray[$key] = $this->generateRecords('int', $this->config->DATAGENERATOR_TINYINT_MAX_SIZE, $noOfRecords, $unique, $reference);
				} else if (strpos($value[0], 'smallint') !== false && $value[1] != 'auto_increment') {
					$returnArray[$key] = $this->generateRecords('int', $this->config->DATAGENERATOR_SMALLINT_MAX_SIZE, $noOfRecords, $unique, $reference);
				} else if (strpos($value[0], 'mediumint') !== false && $value[1] != 'auto_increment') {
					$returnArray[$key] = $this->generateRecords('int', ceil($this->config->DATAGENERATOR_MEDIUMINT_MAX_SIZE / mt_rand(1, 10)), $noOfRecords, $unique, $reference);
				} else if (strpos($value[0], 'bigint') !== false && $value[1] != 'auto_increment') {
					$returnArray[$key] = $this->generateRecords('int', ceil($this->config->DATAGENERATOR_BIGINT_MAX_SIZE / mt_rand(1, 100)), $noOfRecords, $unique, $reference);
				} else if (strpos($value[0], 'int') !== false && $value[1] != 'auto_increment') {
					$returnArray[$key] = $this->generateRecords('int', ceil($this->config->DATAGENERATOR_INT_MAX_SIZE / mt_rand(1, 1000)), $noOfRecords, $unique, $reference);
				// SERIAL is an alias for BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE so we ignore it
				// decimals
				} else if (strpos($value[0], 'float') !== false && $value[1] != 'auto_increment') {
					$returnArray[$key] = $this->generateRecords('float', 0, $noOfRecords, $unique, $reference);
				} else if (strpos($value[0], 'double') !== false && $value[1] != 'auto_increment') {
					$returnArray[$key] = $this->generateRecords('float', 0, $noOfRecords, $unique, $reference);
				} else if (strpos($value[0], 'real') !== false && $value[1] != 'auto_increment') {
					$returnArray[$key] = $this->generateRecords('float', 0, $noOfRecords, $unique, $reference);
				} else if (strpos($value[0], 'decimal') !== false && $value[1] != 'auto_increment') {
					$length = explode(',', str_replace(array('decimal', '(', ')'), '', $value[0]));
					$returnArray[$key] = $this->generateRecords('decimal', array(ceil($length[0] / mt_rand(1, $length[0])), $length[1]), $noOfRecords, $unique, $reference);
				// text
				} else if (strpos($value[0], 'varchar') !== false) {
					$length = str_replace(array('varchar', '(', ')'), '', $value[0]);
					$returnArray[$key] = $this->generateRecords('text', ceil(($length / mt_rand(1, $length)) / $this->config->DATAGENERATOR_VARCHAR_RATIO), $noOfRecords, $unique, $reference);
				} else if (strpos($value[0], 'char') !== false) {
					$returnArray[$key] = $this->generateRecords('text', ceil(($this->config->DATAGENERATOR_TINYTEXT_MAX_SIZE / mt_rand(1, $this->config->DATAGENERATOR_TINYTEXT_MAX_SIZE)) / $this->config->DATAGENERATOR_TINYTEXT_RATIO), $noOfRecords, $unique, $reference);
				} else if (strpos($value[0], 'tinytext') !== false) {
					$returnArray[$key] = $this->generateRecords('text', ceil(($this->config->DATAGENERATOR_TINYTEXT_MAX_SIZE / mt_rand(1, $this->config->DATAGENERATOR_TINYTEXT_MAX_SIZE)) / $this->config->DATAGENERATOR_TINYTEXT_RATIO), $noOfRecords, $unique, $reference);
				} else if (strpos($value[0], 'mediumtext') !== false) {
					$returnArray[$key] = $this->generateRecords('text', ceil(($this->config->DATAGENERATOR_MEDIUMTEXT_MAX_SIZE / mt_rand(1, $this->config->DATAGENERATOR_MEDIUMTEXT_MAX_SIZE)) / $this->config->DATAGENERATOR_MEDIUMTEXT_RATIO), $noOfRecords, $unique, $reference);
				} else if (strpos($value[0], 'longtext') !== false) {
					$returnArray[$key] = $this->generateRecords('text', ceil(($this->config->DATAGENERATOR_LONGTEXT_MAX_SIZE / mt_rand(1, $this->config->DATAGENERATOR_LONGTEXT_MAX_SIZE)) / $this->config->DATAGENERATOR_LONGTEXT_RATIO), $noOfRecords, $unique, $reference);
				} else if (strpos($value[0], 'text') !== false) {
					$returnArray[$key] = $this->generateRecords('text', ceil(($this->config->DATAGENERATOR_TEXT_MAX_SIZE / mt_rand(1, $this->config->DATAGENERATOR_TEXT_MAX_SIZE)) / $this->config->DATAGENERATOR_TEXT_RATIO), $noOfRecords, $unique, $reference);
				// blob
				} else if (strpos($value[0], 'tinyblob') !== false) {
					$returnArray[$key] = $this->generateRecords('text', ceil(($this->config->DATAGENERATOR_TINYTEXT_MAX_SIZE / mt_rand(1, $this->config->DATAGENERATOR_TINYTEXT_MAX_SIZE)) / $this->config->DATAGENERATOR_TINYTEXT_RATIO), $noOfRecords, $unique, $reference);
				} else if (strpos($value[0], 'mediumblob') !== false) {
					$returnArray[$key] = $this->generateRecords('text', ceil(($this->config->DATAGENERATOR_MEDIUMTEXT_MAX_SIZE / mt_rand(1, $this->config->DATAGENERATOR_MEDIUMTEXT_MAX_SIZE)) / $this->config->DATAGENERATOR_MEDIUMTEXT_RATIO), $noOfRecords, $unique, $reference);
				} else if (strpos($value[0], 'longblob') !== false) {
					$returnArray[$key] = $this->generateRecords('text', ceil(($this->config->DATAGENERATOR_LONGTEXT_MAX_SIZE / mt_rand(1, $this->config->DATAGENERATOR_LONGTEXT_MAX_SIZE)) / $this->config->DATAGENERATOR_LONGTEXT_RATIO), $noOfRecords, $unique, $reference);
				} else if (strpos($value[0], 'blob') !== false) {
					$returnArray[$key] = $this->generateRecords('text', ceil(($this->config->DATAGENERATOR_TEXT_MAX_SIZE / mt_rand(1, $this->config->DATAGENERATOR_TEXT_MAX_SIZE)) / $this->config->DATAGENERATOR_TEXT_RATIO), $noOfRecords, $unique, $reference);
				// date and time
				} else if (strpos($value[0], 'datetime') !== false) {
					$returnArray[$key] = $this->generateRecords('datetime', 0, $noOfRecords, $unique, $reference);
				} else if (strpos($value[0], 'date') !== false) {
					$returnArray[$key] = $this->generateRecords('date', 0, $noOfRecords, $unique, $reference);
				} else if (strpos($value[0], 'time') !== false) {
					$returnArray[$key] = $this->generateRecords('time', 0, $noOfRecords, $unique, $reference);
				} else if (strpos($value[0], 'year') !== false) {
					$returnArray[$key] = $this->generateRecords('year', 0, $noOfRecords, $unique, $reference);
				// others
				} else if (strpos($value[0], 'enum') !== false) {
					$values = explode(',', str_replace(array('enum', '(', ')'), '', $value[0]));
					$returnArray[$key] = $this->generateRandomEnum($values, $noOfRecords);
				} else if (strpos($value[0], 'set') !== false) {
					$values = explode(',', str_replace(array('set', '(', ')'), '', $value[0]));
					$returnArray[$key] = $this->generateRandomEnum($values, $noOfRecords);
				} else if (strpos($value[0], 'bit') !== false) {
					$length = str_replace(array('bit', '(', ')'), '', $value[0]);
					$returnArray[$key] = $this->generateRecords('bit', $length, $noOfRecords, $unique, $reference);
				}
			}
		} else {
			throw new RunException('Table requested in data generator doesn\'t exists');
		}

		return $returnArray;
	}

	/**
	 * Generates random records
	 *
	 * @param string $type
	 * @param mixed $length Integer or array for decimal
	 * @param integer $noOfRecords
	 * @param boolean $unique [optional]
	 * @param mixed $reference
	 * @return array Records in array size of $noOfRecords
	 */
	private function generateRecords($type, $length, $noOfRecords, $unique, $reference) {
		$returnArray = array();
		for ($i = 0; $i < $noOfRecords; $i++) {
			if ($unique === true) {
				if ($noOfRecords > $length) $length = $noOfRecords; // to little possibilities

				$isUnique = false;
			} else {
				$isUnique = true;
			}

			if ($reference != null) {
				$returnArray[] = $reference;
				continue;
 			}

			do {
				switch ($type) {
					case 'int':
						$assign = $this->generateRandomInteger($length);
						break;
					case 'float':
						$assign = $this->generateRandomFloat();
						break;
					case 'decimal':
						$assign = $this->generateRandomDecimal($length[0], $length[1]);
						break;
					case 'text':
						$assign = $this->generateRandomString($length);
						break;
					case 'datetime':
						$assign = $this->generateRandomDate('datetime');
						break;
					case 'date':
						$assign = $this->generateRandomDate('date');
						break;
					case 'time':
						$assign = $this->generateRandomDate('time');
						break;
					case 'year':
						$assign = $this->generateRandomDate('year');
						break;
					case 'bit':
						$assign = $this->generateRandomBit($length);
						break;
				}

				if ($isUnique == false) {
					if (in_array($assign, $returnArray)) {
						$isUnique = false;

						// increase $length
						if ((sizeof($returnArray) * 10) > $length) $length *= 10;
					} else {
						$returnArray[] = $assign;
						$isUnique = true;
					}
				} else {
					$returnArray[] = $assign;
				}
			} while ($isUnique === false);
		}

		return $returnArray;
	}

	/**
	 * Returns random integer $length size
	 *
	 * @param integer $length
	 * @return integer
	 */
	private function generateRandomInteger($length) {
		return (integer) mt_rand(0, $length);
	}

	/**
	 * Returns random float $length size
	 *
	 * @param integer $length
	 * @return string Float number
	 */
	private function generateRandomFloat() {
		return (string) '\'' . mt_rand() / 10.0 . '\'';
	}

	/**
	 * Returns random float $length1, $length2 size
	 *
	 * @param integer $length1
	 * @param integer $length2
	 * @return string Float number
	 */
	private function generateRandomDecimal($length1, $length2) {
		return (string) '\'' . number_format(mt_rand(0, pow(10, $length1)) / 10.0, $length2, '.', '') . '\'';
	}

	/**
	 * Returns random string $length size
	 *
	 * @param integer $length
	 * @return string
	 */
	private function generateRandomString($length) {
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$str = '';

		$size = strlen($chars);
		for ($i = 0; $i < $length; $i++) {
			$str .= $chars[mt_rand(0, $size - 1)];
		}

		return (string) '\'' . $str . '\'';
	}

	/**
	 * Generates date for insertion into table
	 * Range: 01.01.1000 00:00:00 - 9999-12-31 23:59:59
	 * @ (error suppression), because timezone is not important for tests
	 *
	 * @param string $type [date, datetime], depending on what we need
	 * @return string Date
	 */
	private function generateRandomDate($type) {
		$int = mt_rand(-30610224000, 253402300799); //

		switch ($type) {
			case 'date':
				return (string) '\'' . @date('Y-m-d', $int) . '\'';
				break;
			case 'datetime':
				return (string) '\'' . @date('Y-m-d H:m:s', $int) . '\'';
				break;
			case 'time':
				return (string) '\'' . @date('H:m:s', $int) . '\'';
				break;
			case 'year':
				return (string) '\'' . @date('Y', $int) . '\'';
				break;
		}
	}

	/**
	 * Generates value for enum or set type
	 *
	 * @param array $values
	 * @param integer $noOfRecords
	 * @return array Records in array size of $noOfRecords
	 */
	public function generateRandomEnum(array $values, $noOfRecords) {
		$returnArray = array();
		for ($i = 0; $i < $noOfRecords; $i++) {
			$randomKey = array_rand($values, 1);
			$returnArray[] = (string) trim($values[$randomKey]);
		}

		return $returnArray;
	}

	/**
	 * Generates random binary number
	 *
	 * @param integer $length
	 * @return string Binary number
	 */
	public function generateRandomBit($length) {
		$random = '';
		for ($k = 0; $k < $length; $k++) {
			$random .= $this->generateRandomInteger(1);
		}

		return (string) 'b \'' . $random . '\'';
	}
}