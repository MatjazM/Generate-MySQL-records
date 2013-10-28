<?php

/**
 * Mysql with normal extension
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Matjaž Mrgole
 */

class MySQLNormal extends MySQL implements MySQLInterface {

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
		$this->DBLink = mysql_connect($serverName, $username, $password);

		if (!$this->DBLink) {
			throw new RunException('Connection with database was unsuccessful: ' . mysql_error());
		} else {
			if (!mysql_select_db($schema, $this->DBLink)) {
				throw new RunException('Select db was unsuccessful: ' . mysql_error());
			} else {
				mysql_query("SET NAMES 'utf8'"); // Set UTF-8 coding
			}
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

		if (!mysql_close($this->DBLink)) {
			throw new RunException('Connection with database didn\'t close successfully: ' . mysql_error());
		}
	}

	/**
	 * Starts transaction
	 *
	 * @throws RunException
	 * @return MySQLNormal object
	 */
	public function startTransaction() {
		if (!mysql_query("START TRANSACTION")) {
			throw new RunException('Starting transaction failed: ' . mysql_error());
		}

		return $this;
	}

	/**
	 * Commits the current transaction
	 *
	 * @throws RunException
	 * @return MySQLNormal object
	 */
	public function commit() {
		if (!mysql_query("COMMIT")) {
			throw new RunException('Commit failed: ' . mysql_error());
		}

		return $this;
	}

	/**
	 * Rolls back current transaction
	 *
	 * @throws RunException
	 * @return MySQLNormal object
	 */
	public function rollback() {
		if (!mysql_query("ROLLBACK")) {
			throw new RunException('Rollback failed: ' . mysql_error());
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
	* @return MysqlNormal object
	*/
	public function query($query, $phs = array(), $silenceException = false) {
		$this->queryCache = mysql_query($this->queryPrepare($query, $phs));
		if (!$this->queryCache) {
			if ($silenceException === false) throw new RunException('Wrong query: ' . mysql_error() . ' Query: ' . $this->queryPrepare($query, $phs));
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
			$query = str_replace('?' . $key . '?', mysql_escape_string($value), $query);
		}

		return $query;
	}

	/**
	 * Returns number of results
	 * 
	 * @return integer Number of rows that we got as an result
	 */
	public function getNoOfRecords() {
		return mysql_num_rows($this->queryCache);
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
			while ($data = mysql_fetch_array($this->queryCache, MYSQL_ASSOC)) {
				$outputData[] = $data;
			}
		} else {
			while ($data = mysql_fetch_array($this->queryCache, MYSQL_ASSOC)) {
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
		return mysql_fetch_array($this->queryCache, MYSQL_ASSOC);
	}
}