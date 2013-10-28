<?php

/**
 * Defines the methods that all implementing classes must have.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Matjaž Mrgole
 */

interface MySQLInterface {

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
	public function __construct($serverName, $username, $password, $schema);

	/**
	 * Closes connection to DB when object is destroyed
	 *
	 * @throws RunException
	 * @return void
	 */
	public function __destruct();

	/**
	 * Starts transaction
	 *
	 * @throws RunException
	 * @return MySQLNormal object
	 */
	public function startTransaction();

	/**
	 * Commits the current transaction
	 *
	 * @throws RunException
	 * @return MySQLNormal object
	 */
	public function commit();

	/**
	 * Rolls back current transaction
	 *
	 * @throws RunException
	 * @return MySQLNormal object
	 */
	public function rollback();

	/**
	* Calls mysql_query() function
	* Parameters: ?qValue? - in $query, array('qValue' => $q) - in $phs
	*
	* @throws RunException
	* @param string $query SQL query
	* @param array $phs [optional]
	* @param boolean $silenceException [optional]
	* @return MySQLNormal object
	*/
	public function query($query, $phs = array(), $silenceException = false);

	/**
	 * Returns number of results
	 *
	 * @return integer Number of rows that we got as an result
	 */
	public function getNoOfRecords();

	/**
	 * Returns all records from a query
	 *
	 * @param string $index If set, then index is ordered by this unique id, increases speed (hash table) [optional]
	 * @return array Array of data
	 */
	public function getRecords($index = '');

	/**
	 * Returns the last record from a query
	 *
	 * @return array Array of data
	 */
	public function getOneRecord();
}
