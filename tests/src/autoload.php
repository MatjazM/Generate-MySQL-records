<?php

/**
 * Autoload for PHPUnit files
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Matjaž Mrgole
 */

error_reporting(E_ERROR | E_WARNING | E_PARSE);
spl_autoload_register('loader');

function loader($className) {
	$fileClass = 'src/library/' . $className . '.class.php';

	if (file_exists($fileClass) === true) {
		require_once($fileClass);
	} else {
		die('Can\'t load file: ' . $className);
	}
}