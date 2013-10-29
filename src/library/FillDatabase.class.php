<?php

/**
 * FillDatabase
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Matjaž Mrgole
 */

class FillDatabase {

	/**
	 * @var Config object
	 */
	private $config;
	/**
	 * @var MysqlNormal object
	 */
	private $mysql;
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
	 * @param ConfigInterface $config
	 * @param MysqlInterface $mysql
	 * @param array $parameters
	 * @returns void
	 */
	public function __construct(ConfigInterface $config, MysqlInterface $mysql) {
		$this->config = $config;
		$this->mysql = $mysql;
	}

	/**
	 * Based on user input executes generation of random records in tables
	 *
	 * @returns void
	 */
	public function fillDatabase() {
		$this->loadSchema();

		if (empty($this->columns)) {
			echo PHP_EOL . "No tables in schema" . PHP_EOL;
		} else {
			echo PHP_EOL . "Enter number of random records you would like to insert into following tables (press enter if you would like to skip table):" . PHP_EOL;

			$tables = array();
			foreach ($this->columns as $table => $columns) {
				$input = Run::readInputCLI('- ' . $table . ': ', false);

				if (is_numeric($input) && !empty($input)) $tables[$table] = $input;
			}

			$confirm = Run::readInputCLI('Are you sure you would like to fill stated tables in database with random records? Type \'yes\' if you do: ', true, 'yes');

			if ($confirm === true) {
				$benchmark = new Benchmark();
				$dataGenerator = new DataGenerator($this->columns, $this->indexes, $this->config);
				$memoryUsage = new MemoryUsage($this->config, $this->columns);

				$benchmark->startTime('totalTime');

				$$noOfRecordsSum = 0;
				foreach ($tables as $table => $noOfRecords) {
					$echoStr = 'Filling table \'' . $table . '\' with random data can take some time. Please wait...';
					echo $echoStr;

					$memoryUsageTable = $memoryUsage->calculateMaxMemoryUsage($table, $noOfRecords);

					if (($memoryUsageTable / 1000000) > 1) { // devide in multiple jobs, otherwise we hit PHP memory limit
						$generateITimes = ceil(($memoryUsageTable / 1000000));
						$noOfRecordsNew = round($noOfRecords / $generateITimes);

						for ($i = 0; $i < $generateITimes; $i++) {
							$data = $dataGenerator->generateData($table, $noOfRecordsNew);
							$this->mysql->query($this->generateQuery($table, $data), array(), true);
						}
					} else {
						$data = $dataGenerator->generateData($table, $noOfRecords);
						$this->mysql->query($this->generateQuery($table, $data), array(), true);
					}

					$noOfRecordsSum += $noOfRecords;

					Run::removeCharacters(strlen($echoStr));
				}

				$benchmark->endTime('totalTime');

				echo PHP_EOL . Run::success('Done filling tables with random data.') . PHP_EOL;
				echo '----------------------------------------' . PHP_EOL;
				echo 'Total time: ' . $benchmark->showTime('totalTime', 0) . ' seconds, ';
				echo 'Memory: ' . number_format(memory_get_peak_usage(true) / 1048576, 2) . ' MB' . PHP_EOL;
				echo 'Number of records inserted: ' . $noOfRecordsSum . PHP_EOL;
				echo '----------------------------------------' . PHP_EOL;
			}
		}
	}

	/**
	 * Saves tables, their columns and their indexes
	 *
	 * @returns void
	 */
	private function loadSchema() {
		$this->mysql->query("USE " . $this->config->MYSQL_SCHEMA);

		$tables = $this->mysql->query("SELECT table_name, engine, table_type FROM INFORMATION_SCHEMA.TABLES WHERE table_schema = DATABASE()")->getRecords();
		foreach ($tables as $table) {
			$noOfRecords = $this->mysql->query("SELECT count(*) AS noOfRecords FROM " . $table['table_name'])->getOneRecord();
			$firstRecord = $this->mysql->query("SELECT * FROM " . $table['table_name'] . " LIMIT 0, 1")->getOneRecord(); // we use it for foreign key reference

			$this->columns[$table['table_name']]['storageType'] = $table['engine'];
			$this->columns[$table['table_name']]['table_type'] = $table['table_type'];
			$this->columns[$table['table_name']]['noOfRecords'] = $noOfRecords['noOfRecords'];
			$this->columns[$table['table_name']]['firstRecord'] = $firstRecord;

			$foreignConstrains = $this->mysql->query("SELECT column_name, referenced_table_name, referenced_column_name FROM INFORMATION_SCHEMA.key_column_usage " .
													 "WHERE referenced_table_schema = '" . $this->config->MYSQL_SCHEMA . "' AND table_name = 'forecastDataShort' " .
													 "AND referenced_table_name IS NOT NULL")->getRecords();

			// Save columns
			$columns = $this->mysql->query("SHOW COLUMNS FROM " . $table['table_name'])->getRecords();
			foreach ($columns as $column) {
				if (!empty($foreignConstrains)) {
					$reference = null;

					foreach ($foreignConstrains as $constraint) {
						if ($constraint['column_name'] == $column['Field']) {
							$reference = array($constraint['referenced_table_name'], $constraint['referenced_column_name']);
						}
					}
				}

				if ($reference !== null) {
					$this->columns[$table['table_name']][$column['Field']] = array($column['Type'], $column['Extra'], $reference);
				} else {
					$this->columns[$table['table_name']][$column['Field']] = array($column['Type'], $column['Extra']);
				}
			}

			// Save indexes
			$indexes = $this->mysql->query("SHOW INDEX FROM " . $table['table_name'])->getRecords();
			$keyName = '';
			foreach ($indexes as $index) {
				if ($keyName != $index['Key_name']) {
					$keyName = $index['Key_name'];
				}

				$this->indexes[$table['table_name']][$keyName]['unique'] = (boolean) !$index['Non_unique'];
				$this->indexes[$table['table_name']][$keyName]['indexes'][] = $index['Column_name'];
			}
		}

		// Correct foreign constrains
		foreach ($this->columns as  &$columns) {
			foreach ($columns as &$column) {
				if (is_array($column) && isset($column[2])) {
					$column[2] = $this->columns[$column[2][0]]['firstRecord'][$column[2][1]];
				}
			}
		}
	}

	/**
	 * Generates query for inserting data to $table from $data
	 *
	 * @param string $table
	 * @param array $data
	 * @return string Query
	 */
	private function generateQuery($table, array $data) {
		$query = 'INSERT INTO ' . $table;

		$columnsArray = array();
		foreach ($data as $key => $value) {
			$columnsArray[] = $key;
		}
		$columns = implode(',', $columnsArray);

		$query .= ' (' . $columns . ') VALUES ';

		$sizeOfValues = sizeof($data[$columnsArray[0]]);
		$sizeOfColumns = sizeof($columnsArray);
		for ($i = 0; $i < $sizeOfValues; $i++) {
			$values = '(';
			for ($j = 0; $j < $sizeOfColumns; $j++) {
				 $values .= $data[$columnsArray[$j]][$i] . ',';
			}
			$values = substr($values, 0, -1);
			$values .= ')';

			$query .= $values . ',';
		}
		$query = substr($query, 0, -1);

		return $query;
	}
}
