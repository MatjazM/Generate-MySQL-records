<?php

/**
 * Mysql helper
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Matjaž Mrgole
 */

class MySQL {

	/**
	 * MySQL connection
	 * @var object
	 */
	protected $DBLink;
	/**
	 * Returned result resource
	 * @var integer
	 */
	protected $queryCache = array();
	/**
	 * Holds results
	 * @var mixed
	 */
	protected $resultCache = array();
	/**
	 * Counts the number of queries executed
	 * @var integer
	 */
	public $noOfQueries = 0;
}