<?php

/**
 * Simple benchmark class
 * Modified version of https://github.com/MatjazM/Simple-PHP-benchmark
 */

class Benchmark {

	/**
	 * @var array
	 */
	private $startTime = array();
	/**
	 * @var array
	 */
	private $savedTimes = array();

	/**
	 * Starts benchmark
	 *
	 * @throws RunException
	 * @param string $group savedTimes[$group] [optional]
	 * @return void
	 */
	public function startTime($group = '') {
		if (!isset($this->startTime[$group])) {
			$this->startTime[$group] = microtime(true);
		} else {
			throw new RunException('You must first call endTime method.');
		}
	}

	/**
	 * Ends benchmark
	 *
	 * @throws RunException
	 * @param string $group savedTimes[$group] [optional]
	 * @return void
	 */
	public function endTime($group = '') {
		if (isset($this->startTime[$group])) {
			$this->savedTimes[$group][] = microtime(true) - $this->startTime[$group];
			$this->startTime[$group] = null;
		} else {
			throw new RunException('You must first call startTime method.');
		}
	}

	/**
	 * Shows particular benchmark (selected with group and index)
	 *
	 * @throws RunException
	 * @param string $group savedTimes[$group][] [optional]
	 * @param integer $index savedTimes[][$index]
	 * @param integer $size How many decimals for number format [optional]
	 * @return string Formatted benchmark time
	 */
	public function showTime($group = '', $index, $size = 0) {
		if (isset($this->savedTimes[$group][$index])) {
			return (string) number_format($this->savedTimes[$group][$index], $size);
		} else {
			throw new RunException('Benchmark with group ' . $group . ' and index ' . $index . ' doesn\'t exist.');
		}
	}
}