<?php

/**
 * CLI interface
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Matjaž Mrgole
 */

class Run {

	/**
	 * Relative path to config file
	 * @var string
	 */
	const CONFIG_FILE = 'config/config.conf.php';

	/**
	* @var string
	*/
	private $mode = 'runInfo';
	/**
	 * @var Config object
	 */
	private $config;
	/**
	 * @var MysqlNormal object
	 */
	private $mysql;
	/**
	 * @var FileManipulation object
	 */
	private $fileManipulation;

	/**
	 * @return void
	 */
	public function __construct() {
		$this->checkCLI();

		echo $this->info('Generate MySQL records v0.1 beta') . PHP_EOL;

		$this->loadConfig();
		$this->loadFileManipulation();

		$this->mode = $this->detectMode();
		$this->run();
	}

	/**
	 * Prevent script from being called via browser
	 *
	 * @throws RunException
	 * @return void
	 */
	private function checkCLI() {
		if (PHP_SAPI !== 'cli') {
			throw new RunException('You may only run from the command line', 1);
		}
	}

	/**
	 * Loads config file
     * $config is defined in config.conf.php
	 *
	 * @throws RunException
	 * @return void
	 */
	private function loadConfig() {
		if (file_exists(self::CONFIG_FILE) === true) {
			include_once self::CONFIG_FILE;
		} else {
			throw new RunException('Config file doesn\'t exists');
		}

        /** @var $config array */
        $this->config = new Config($config);
	}

	/**
	 * Loads mysql class
	 *
	 * @throws RunException
	 * @return void
	 */
	private function loadMySQL() {
		$mysqlOK = extension_loaded('mysql');
		$mysqliOK = extension_loaded('mysqli');

		if ($mysqliOK) {
			$this->mysql = new MySQLImproved($this->config->MYSQL_HOSTNAME, $this->config->MYSQL_USERNAME, $this->config->MYSQL_PASSWORD, $this->config->MYSQL_SCHEMA);
		} else if ($mysqlOK) {
			$this->mysql = new MySQLNormal($this->config->MYSQL_HOSTNAME, $this->config->MYSQL_USERNAME, $this->config->MYSQL_PASSWORD, $this->config->MYSQL_SCHEMA);
		} else {
			throw new RunException('No mysql support.');
		}
	}

	/**
	 * Loads FileManipulation class
	 *
	 * @return void
	 */
	private function loadFileManipulation() {
		$this->fileManipulation = new FileManipulation();
	}

	/**
	* Detects mode from arguments from CLI
	*
	* @return string
	*/
	private function detectMode() {
		$arguments = $_SERVER['argv'];

		$mode = 'runInfo';

		if (!isset($arguments[1])) {
			return $mode;
		}

		switch ($arguments[1]) {
			case '--setupConfig':
				$mode = 'runSetupConfig';
				break;
			case '--fillRecords':
				$mode = 'runFillRecords';
				break;
			case '--compatibilityTest':
				$mode = 'runCompatibilityTest';
				break;
			case '--info':
				$mode = 'runInfo';
				break;
			default:
				$mode = 'runInfo';
				break;
		}

		return $mode;
	}

	/**
	 * Runs the command appropriate to $this->mode
	 *
	 * @return void
	 */
	private function run() {
		switch ($this->mode) {
			case 'runInfo':
				$this->runInfo();
				break;
			case 'runSetupConfig':
				$this->runSetupConfig();
				break;
			case 'runFillRecords':
				$this->runFillRecords();
				break;
			case 'runCompatibilityTest':
				$this->runCompatibilityTest();
				break;
		}
	}

	/**
	 * Shows available commands from CLI
	 *
	 * @return void
	 */
	private function runInfo() {
		echo PHP_EOL .
			'Usage: php run.php [switches]' . PHP_EOL . PHP_EOL .
			'  --info                    Shows all possible commands.' . PHP_EOL .
			'  --compatibilityTest       Runs compatibility test.' . PHP_EOL .
			'  --fillRecords             Fills database tables with records.' . PHP_EOL .
			'  --setupConfig             Sets config.conf.php file. ' . PHP_EOL;
	}

	/**
	 * Updates config.conf.php file with arguments from CLI
	 *
	 * @return void
	 */
	private function runSetupConfig() {
		$parameters = array();
		$parameters['MYSQL_HOSTNAME'] = Run::readInputCLI('Enter MySQL hostname: ', false);
		$parameters['MYSQL_USERNAME'] = Run::readInputCLI('Enter MySQL username: ', false);
		$parameters['MYSQL_PASSWORD'] = Run::readInputCLI('Enter MySQL password: ', false);
		$parameters['MYSQL_SCHEMA'] = Run::readInputCLI('Enter MySQL schema: ', false);

		function removeBadCharacters(&$item, $key, $prefix) {
			$item = str_replace('\'', '', $item);
		}

		array_walk($parameters, 'removeBadCharacters'); // otherwise config file could have parse error

		$this->fileManipulation->replaceFile(self::CONFIG_FILE, array(
			17 => '$config[\'MYSQL_HOSTNAME\'] = \'' . $parameters['MYSQL_HOSTNAME'] . '\';',
			18 => '$config[\'MYSQL_USERNAME\'] = \'' . $parameters['MYSQL_USERNAME'] . '\';',
			19 => '$config[\'MYSQL_PASSWORD\'] = \'' . $parameters['MYSQL_PASSWORD'] . '\';',
			20 => '$config[\'MYSQL_SCHEMA\'] = \'' . $parameters['MYSQL_SCHEMA'] . '\';'
		));

		echo $this->success('Config file was updated.') . PHP_EOL;
	}

	/**
	 * Checks if CLI has minimum requirements for using Automatic index setting for PHP and MySQL.
	 *
	 * @return void
	 */
	private function runCompatibilityTest() {
		$phpOK = (function_exists('version_compare') && version_compare(phpversion(), '5.1.0', '>='));
		$MySQLOK = extension_loaded('mysql');
		$MySQLiOK = extension_loaded('mysqli');
		if ($phpOK && ($MySQLOK || $MySQLiOK)) $compatibility = true; else $compatibility = false;

		echo PHP_EOL;
		echo 'PHP Environment Compatibility Test (CLI)' . PHP_EOL;
		echo '----------------------------------------' . PHP_EOL;
		echo PHP_EOL;

		echo 'PHP 5.1 or newer............ ' . ($phpOK ? (Run::success() . ' ' . phpversion()) : Run::failure()) . PHP_EOL;
		echo 'MySQL....................... ' . ($MySQLOK ? Run::success() : Run::failure()) . PHP_EOL;
		echo 'MySQLi...................... ' . ($MySQLiOK ? Run::success() : Run::failure()) . PHP_EOL;

		echo PHP_EOL;
		echo '----------------------------------------' . PHP_EOL;
		echo PHP_EOL;

		if ($compatibility) {
			echo Run::success('Your environment meets the minimum requirements.') . PHP_EOL . PHP_EOL;
		} else {
			if (!$phpOK) echo '* ' . Run::failure('PHP:') . ' You are running an unsupported version of PHP.' . PHP_EOL . PHP_EOL;
			if (!$MySQLOK) echo '* ' . Run::failure('MySQL:') . ' MySQL support is not available.' . PHP_EOL . PHP_EOL;
			if (!$MySQLiOK) echo '* ' . Run::failure('MySQLi:') . ' MySQLi support is not available.' . PHP_EOL . PHP_EOL;
		}
	}

	/**
	 * @return void
	 */
	public function runFillRecords() {
		$this->loadMySQL();

		$fillDatabase = new FillDatabase($this->config, $this->mysql);
		$fillDatabase->fillDatabase();
	}

	/**
	 * @static
	 * @return boolean
	 */
	public static function isWindows() {
		return strtolower(substr(PHP_OS, 0, 3)) === 'win';
	}

	/**
	 * @param string $s [optional]
	 * @static
	 * @return string
	 */
	public static function success($s = 'Yes') {
		return Run::isWindows() ? $s : "\033[1;37m\033[42m " . $s . " \033[0m";
	}

	/**
	 * @param string $s [optional]
	 * @static
	 * @return string
	 */
	public static function info($s = 'Info') {
		return Run::isWindows() ? $s : "\033[1;37m\033[44m " . $s . " \033[0m";
	}

	/**
	 * @param string $s [optional]
	 * @static
	 * @return string
	 */
	public static function failure($s = 'No ') {
		return Run::isWindows() ? $s : "\033[1;37m\033[41m " . $s . " \033[0m";
	}

	/**
	 * Removes $size characters from CLI
	 *
	 * @param integer $size
	 * @returns void
	 */
	public static function removeCharacters($size) {
		for ($i = 0; $i < $size; $i++) echo "\x08"; // (hex:08) is backspace
		for ($i = 0; $i < $size; $i++) echo " ";
		for ($i = 0; $i < $size; $i++) echo "\x08";
	}

	/**
	 * @param string $output
	 * @param boolean $returnBoolean If true, boolean value will be returned, otherwise input from CLI
	 * @param mixed $compare Value to compare with input from CLI if $returnBoolean is true [optional]
	 * @return mixed Boolean or string
	 */
	public static function readInputCLI($output, $returnBoolean, $compare = '') {
		echo $output;

		$handle = fopen('php://stdin', 'r');
		$line = trim(fgets($handle));

		if ($returnBoolean === true) {
			if ($line == $compare) return true; else return false;
		} else {
			return $line;
		}
	}
}