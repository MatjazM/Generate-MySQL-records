<?php

/**
 * CLI interface
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Matjaž Mrgole
 */

error_reporting(E_ERROR);

/**
 * Magic method for autoloading classes
 *
 * @throws RunException
 * @param string $className
 * @return void
 */
function __autoload($className) {
	$fileClass = 'library/' . $className . '.class.php';

	try {
		if (file_exists($fileClass) === true) {
			require_once($fileClass);
		} else {
			throw new RunException('Class ' . $fileClass . ' doesn\'t load');
		}
	} catch (RunException $e) {
		$e->showMessage();
	}
}

try {
	new Run();
}  catch (RunException $e) {
	$e->showMessage();
}