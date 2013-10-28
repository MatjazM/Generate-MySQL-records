<?php

/**
 * MemoryUsage for calculating how much memory does a randomly generated record takes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Matjaž Mrgole
 */

class MemoryUsage {

	/**
	 * @var Config object
	 */
	private $config;
	/**
	 * Stores data about database tables
	 * @var array
	 */
	private $columns = array();

	/**
	 * @param ConfigInterface $config
	 * @param array $columns Stores data about database tables
	 * @return void
	 */
	public function __construct(ConfigInterface $config, $columns) {
		$this->config = $config;
		$this->columns = $columns;
	}

	/**
	 * Calculates memory usage
	 *
	 * @throws RunException
	 * @param $table
	 * @param $noOfRecords
	 * @return integer Size of memory in bytes that randomly generated records will take
	 */
	public function calculateMaxMemoryUsage($table, $noOfRecords) {
		$memoryUsage = 0;

		if (isset($this->columns[$table])) {
			foreach ($this->columns[$table] as $key => $value) {
				// integers
				if (strpos($value[0], 'tinyint') !== false && $value[1] != 'auto_increment') {
					$memoryUsage += 48;
				} else if (strpos($value[0], 'smallint') !== false && $value[1] != 'auto_increment') {
					$memoryUsage += 48;
				} else if (strpos($value[0], 'mediumint') !== false && $value[1] != 'auto_increment') {
					$memoryUsage += 48;
				} else if (strpos($value[0], 'bigint') !== false && $value[1] != 'auto_increment') {
					$memoryUsage += 48;
				} else if (strpos($value[0], 'int') !== false && $value[1] != 'auto_increment') {
					$memoryUsage += 48;
				// decimals
				} else if (strpos($value[0], 'float') !== false && $value[1] != 'auto_increment') {
					$memoryUsage += 88;
				} else if (strpos($value[0], 'double') !== false && $value[1] != 'auto_increment') {
					$memoryUsage += 88;
				} else if (strpos($value[0], 'real') !== false && $value[1] != 'auto_increment') {
					$memoryUsage += 88;
				} else if (strpos($value[0], 'decimal') !== false && $value[1] != 'auto_increment') {
					$length = explode(',', str_replace(array('decimal', '(', ')'), '', $value[0]));

					$memoryUsage += $this->calculateSizeOfString($length[0] + $length[1] + 1); // 1 is comma
				// text
				} else if (strpos($value[0], 'varchar') !== false) {
					$length = str_replace(array('varchar', '(', ')'), '', $value[0]);

					$memoryUsage += $this->calculateSizeOfString(ceil($length / $this->config->DATAGENERATOR_VARCHAR_RATIO));
				} else if (strpos($value[0], 'char') !== false) {
					$memoryUsage += $this->calculateSizeOfString(ceil($this->config->DATAGENERATOR_TINYTEXT_MAX_SIZE / $this->config->DATAGENERATOR_TINYTEXT_RATIO));
				} else if (strpos($value[0], 'tinytext') !== false) {
					$memoryUsage += $this->calculateSizeOfString(ceil($this->config->DATAGENERATOR_TINYTEXT_MAX_SIZE / $this->config->DATAGENERATOR_TINYTEXT_RATIO));
				} else if (strpos($value[0], 'mediumtext') !== false) {
					$memoryUsage += $this->calculateSizeOfString(ceil($this->config->DATAGENERATOR_MEDIUMTEXT_MAX_SIZE / $this->config->DATAGENERATOR_MEDIUMTEXT_RATIO));
				} else if (strpos($value[0], 'longtext') !== false) {
					$memoryUsage += $this->calculateSizeOfString(ceil($this->config->DATAGENERATOR_LONGTEXT_MAX_SIZE / $this->config->DATAGENERATOR_LONGTEXT_RATIO));
				} else if (strpos($value[0], 'text') !== false) {
					$memoryUsage += $this->calculateSizeOfString(ceil($this->config->DATAGENERATOR_TEXT_MAX_SIZE / $this->config->DATAGENERATOR_TEXT_RATIO));
				// blob
				} else if (strpos($value[0], 'tinyblob') !== false) {
					$memoryUsage += $this->calculateSizeOfString(ceil($length / $this->config->DATAGENERATOR_VARCHAR_RATIO));
				} else if (strpos($value[0], 'mediumblob') !== false) {
					$memoryUsage += $this->calculateSizeOfString(ceil($this->config->DATAGENERATOR_MEDIUMTEXT_MAX_SIZE / $this->config->DATAGENERATOR_MEDIUMTEXT_RATIO));
				} else if (strpos($value[0], 'longblob') !== false) {
					$memoryUsage += $this->calculateSizeOfString(ceil($this->config->DATAGENERATOR_LONGTEXT_MAX_SIZE / $this->config->DATAGENERATOR_LONGTEXT_RATIO));
				} else if (strpos($value[0], 'blob') !== false) {
					$memoryUsage += $this->calculateSizeOfString(ceil($this->config->DATAGENERATOR_TEXT_MAX_SIZE / $this->config->DATAGENERATOR_TEXT_RATIO));
				// date and time
				} else if (strpos($value[0], 'datetime') !== false) {
					$memoryUsage += 88;
				} else if (strpos($value[0], 'date') !== false) {
					$memoryUsage += 80;
				} else if (strpos($value[0], 'time') !== false) {
					$memoryUsage += 80;
				} else if (strpos($value[0], 'year') !== false) {
					$memoryUsage += 80;
				// others
				} else if (strpos($value[0], 'enum') !== false) {
					$values = explode(',', str_replace(array('enum', '(', ')'), '', $value[0]));
					$lengths = array_map('strlen', $values);

					$memoryUsage += $this->calculateSizeOfString(max($lengths));
				} else if (strpos($value[0], 'set') !== false) {
					$values = explode(',', str_replace(array('set', '(', ')'), '', $value[0]));
					$lengths = array_map('strlen', $values);

					$memoryUsage += $this->calculateSizeOfString(max($lengths));
				} else if (strpos($value[0], 'bit') !== false) {
					$length = str_replace(array('bit', '(', ')'), '', $value[0]);

					$memoryUsage += $this->calculateSizeOfString($length + 4); // 4 is extra characters (b '')
				}
			}
		} else {
			throw new RunException('Table requested in data generator doesn\'t exists');
		}

		return $memoryUsage * $noOfRecords;
	}

	/**
	 * @param integer $length Size of string
	 * @return integer Size of string in bytes
	 */
	private function calculateSizeOfString($length) {
		return 80 + (floor($length / 16) * 8); // empty string 80 bytes + 8 bytes / 15 character
	}
}