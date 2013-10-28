<?php

/**
 * Unit testing for FileManipulation.class.php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Matjaž Mrgole
 */

require_once 'tests/src/autoload.php';
require_once 'vfsStream/vfsStream.php';

class FileManipulationTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var FileManipulation object
	 */
	private $fileManipulation;

	/**
	 * Set up vfsStream and FileManipulation object
	 *
	 * @return void
	 */
	protected function setUp() {
		vfsStreamWrapper::register();
		vfsStreamWrapper::setRoot(new vfsStreamDirectory('unitTestDir'));

		$this->fileManipulation = new FileManipulation();
	}

	/**
	 * Destroys FileManipulation object
	 *
	 * @returns void
	 */
	protected function tearDown() {
		unset($this->fileManipulation);
	}

	/**
	 * getFile exception
	 *
	 * @expectedException RunException
	 * @return void
	 */
	public function testGetFileException() {
		$this->fileManipulation->getFile(vfsStream::url('unitTestDir/testFile'));
	}

	/**
	 * Test getFile method
	 *
	 * @return void
	 */
	public function testGetFile() {
		$file = vfsStream::newFile('testFile');
		$root = vfsStreamWrapper::getRoot();
		$root->addChild($file);

		$this->assertTrue(vfsStreamWrapper::getRoot()->hasChild('testFile'));

		$file->setContent('testLine');

		$this->assertEquals($this->fileManipulation->getFile(vfsStream::url('unitTestDir/testFile')), array(0 => 'testLine'));
	}

	/**
	 * writeToFile exception
	 *
	 * @expectedException RunException
	 * @return void
	 */
	public function testWriteToFileArrayException() {
		$this->fileManipulation->writeToFileArray(vfsStream::url('unitTestDir/testFile'), array(1 => 'replace'));
	}

	/**
	 * writeToFile exception for permission
	 *
	 * @expectedException RunException
	 * @return void
	 */
	public function testWriteToFileArrayExceptionPermission() {
	   $file = vfsStream::newFile('testFile', 0444);
	   $root = vfsStreamWrapper::getRoot();
	   $root->addChild($file);

	   $this->assertTrue(vfsStreamWrapper::getRoot()->hasChild('testFile'));

	   $this->fileManipulation->writeToFileArray(vfsStream::url('unitTestDir/testFile'), array(1 => 'replace'));
	}

	/**
	 * replaceFile test
	 *
	 * @return void
	 */
	public function testReplaceFile() {
		$file = vfsStream::newFile('testFile');
		$root = vfsStreamWrapper::getRoot();
		$root->addChild($file);

		$this->assertTrue(vfsStreamWrapper::getRoot()->hasChild('testFile'));

		$file->setContent('notReplaced' . "\n" . 'notReplaced2' . "\n"  . 'notReplaced3');
		$this->fileManipulation->replaceFile(vfsStream::url('unitTestDir/testFile'), array(2 => 'replace'));

		$this->assertEquals($file->getContent(), 'notReplaced'  . "\n" . 'replace' . "\n" . 'notReplaced3');
	}

	/**
	 * writeToFile replace test
	 *
	 * @return void
	 */
	public function testWriteToFile() {
		$file = vfsStream::newFile('testFile');
		$root = vfsStreamWrapper::getRoot();
		$root->addChild($file);

		$this->assertTrue(vfsStreamWrapper::getRoot()->hasChild('testFile'));

		$file->setContent('notReplaced');
		$this->fileManipulation->writeToFileArray(vfsStream::url('unitTestDir/testFile'), array(1 => 'replace',
																								2 => 'replace2',
																								3 => 'newLine'));

		$this->assertEquals($file->getContent(), 'replace'  . "\n" . 'replace2' . "\n" . 'newLine');
	}
}