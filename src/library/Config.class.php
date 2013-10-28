<?php

/**
 * Config contains all the configuration for the system.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Matjaž Mrgole
 */

class Config implements ConfigInterface {

	/**
	 * @param array $config Config variables
	 */
	public function __construct(array $config) {
		$this->load($config);
	}

	/**
	 * Set the configurations.
	 * 
	 * @param array $config Config variables (array('variable' => 'value'))
	 */
	private function load(array $config) {
		foreach ($config as $key => $value) {
			$this->set($key, $value, false);
		}
	}

	/**
	 * Add data to be retrieved later on. Served as a dummy storage.
	 *
	 * @throws RunException
	 * @param string $key
	 * @param string $value
	 * @param boolean $override If true, it will override previous setting [optional]
	 * @return void
	 */
	public function set($key, $value, $override = false) {
		if ($override === false) {
			if (isset($this->{$key})) {
				throw new RunException('Key can\'t be overridden.');
			}
		}

		$this->{$key} = $value;
	}

	/**
	 * Magic method for getting data from class
	 *
	 * @throws RunException
	 * @param string $key
	 * @return string Key value
	 */
	public function __get($key) {
		if (isset($this->{$key})) {
			return $this->{$key};
		} else {
			throw new RunException('Key not set.');
		}
	}
}