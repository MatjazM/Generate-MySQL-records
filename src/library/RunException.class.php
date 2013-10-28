<?php

/**
 * Custom exception handler
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Matjaž Mrgole
 */

class RunException extends Exception {

	/**
	* Construct the exception
	*
	* @param string $message
	* @param integer $code [optional]
	* @param Exception $previous [optional]
	*/
	public function __construct($message, $code = 0, Exception $previous = null) {
		parent::__construct($message, $code, $previous);
	}

	/**
	 * @return void
	 */
	function showMessage() {
		switch ($this->getCode()) {
			case 0: // CLI
				echo Run::failure('ERROR: ' . $this->getMessage()) . PHP_EOL;

				break;
			case 1: // Browser
				echo 'ERROR: ' . $this->getMessage();

				break;
		}

		exit;
	}
}