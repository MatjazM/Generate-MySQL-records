<?php

/**
 * Class for file manipulation
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Matjaž Mrgole
 */

class FileManipulation {

	/**
	 * Returns file contents inside an array
	 *
	 * @throws RunException
	 * @param string $fileLocation
	 * @return array File content in array
	 */
	public function getFile($fileLocation) {
		if (file_exists($fileLocation)) {
			return file($fileLocation, FILE_IGNORE_NEW_LINES);
		} else {
			throw new RunException('File ' . $fileLocation . ' doesn\'t exists.');
		}
	}

	/**
	 * Replaces particular lines in file
	 * If you need to change the length of the line, there is no way out of rewriting at least all of the file after the changed line.
	 *
	 * @throws RunException
	 * @param string $fileLocation
	 * @param array $replace
	 * @return void
	 */
	public function replaceFile($fileLocation, array $replace) {
		$arrayOfLines = file($fileLocation, FILE_IGNORE_NEW_LINES);

		foreach ($replace as $line => $content) {
			$arrayOfLines[$line - 1] = $content;
		}

		$this->writeToFileArray($fileLocation, $arrayOfLines);
	}

	/**
	 * Writes to file from array of lines
	 *
	 * @throws RunException
	 * @param string $fileLocation
	 * @param array $content
	 * @return void
	 */
	public function writeToFileArray($fileLocation, array $content) {
		if (file_exists($fileLocation)) {
			if (is_writable($fileLocation)) {
				$handle = fopen($fileLocation, 'w');

				if ($handle) fwrite($handle, implode("\n", $content));

				fclose($handle);
			} else {
				throw new RunException('File ' . $fileLocation . ' doesn\'t have write permission.');
			}
		} else {
			throw new RunException('File ' . $fileLocation . ' doesn\'t exists.');
		}
	}
}