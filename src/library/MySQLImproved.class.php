<?php

/**
 * MySQL Improved Extension
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Matjaž Mrgole
 */

class MySQLImproved extends MySQL implements MySQLInterface {

	/**
	 * Connects to database
	 *
	 * @throws RunException
	 * @param string $serverName
	 * @param string $username
	 * @param string $password
	 * @param string $schema
	 * @returns void
	 */
	public function __construct($serverName, $username, $password, $schema) {
		$this->DBLink = new mysqli($serverName, $username, $password, $schema);

		if (mysqli_connect_error()) {
			throw new RunException('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
		} else {
			mysql_query("SET NAMES 'utf8'"); // Set UTF-8 coding
		}
	}

	/**
	 * Closes connection to DB when object is destroyed
	 *
	 * @throws RunException
	 * @return void
	 */
	public function __destruct() {
		$this->rollback(); // If something goes wrong, we need to put database to it's original state

		if (!$this->DBLink->close()) {
			throw new RunException('Connection with database didn\'t close successfully: ' . $this->DBLink->error());
		}
	}

	/**
	 * Starts transaction
	 *
	 * @throws RunException
	 * @return MySQLImproved object
	 */
	public function startTransaction() {
		if (!$this->DBLink->query("START TRANSACTION")) {
			throw new RunException('Starting transaction failed: ' . $this->DBLink->error());
		}
	}

	/**
	 * Commits the current transaction
	 *
	 * @throws RunException
	 * @return MySQLImproved object
	 */
	public function commit() {
		if (!$this->DBLink->commit()) {
			throw new RunException('Commit failed: ' . $this->DBLink->error());
		}

		return $this;
	}

	/**
	 * Rolls back current transaction
	 *
	 * @throws RunException
	 * @return MySQLImproved object
	 */
	public function rollback() {
		if (!$this->DBLink->rollback()) {
			throw new RunException('Rollback failed: ' . $this->DBLink->error());
		}

		return $this;
	}

	/**
	* Calls mysql_query() function
	* Parameters: ?qValue? - in $query, array('qValue' => $q) - in $phs
	*
	* @throws RunException
	* @param string $query SQL query
	* @param array $phs [optional]
	* @param boolean $silenceException [optional]
	* @return MySQLImproved object
	*/
	public function query($query, $phs = array(), $silenceException = false) {
		$this->queryCache = $this->DBLink->query($this->queryPrepare($query, $phs));
		if (!$this->queryCache) {
			if ($silenceException === false) throw new RunException('Wrong query: ' . $this->DBLink->error() . ' Query: ' . $this->queryPrepare($query, $phs));
		} else {
			$this->noOfQueries++;
		}

		return $this;
	}

	/**
	 * Prepares query, so we can use parameters
	 * Use: ?qValue?
	 * 
	 * @param string $query SQL query
	 * @param array $phs [optional]
	 * @return string $query Replaced string with mysql_escape_string
	 */
	private function queryPrepare($query, $phs = array()) {
		foreach ($phs as $key => $value) {
			$query = str_replace('?' . $key . '?', $this->DBLink->real_escape_string($value), $query);
		}

		return $query;
	}

	/**
	 * Returns number of results
	 * 
	 * @return integer Number of rows that we got as an result
	 */
	public function getNoOfRecords() {
		return $this->DBLink->num_rows;
	}

	/**
	 * Returns all records from a query
	 *
	 * @param string $index If set, then index is ordered by this unique id, increases speed (hash table) [optional]
	 * @return array Array of data
	 */
	public function getRecords($index = '') {
		$outputData = array();
		$data = null;

		if ($index == '') {
			while ($data = $this->DBLink->fetch_accos()) {
				$outputData[] = $data;
			}
		} else {
			while ($data = $this->DBLink->fetch_accos()) {
				$outputData[$data[$index]] = $data;
			}
		}

		$this->resultCache = $outputData;

		return $this->resultCache;
	}

	/**
	 * Returns the last record from a query
	 * 
	 * @return array Array of data
	 */
	public function getOneRecord() {
		return $this->DBLink->fetch_accos();
	}
}