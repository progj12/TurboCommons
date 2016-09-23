<?php

/**
 * TurboCommons is a general purpose and cross-language library that implements frequently used and generic software development tasks.
 *
 * Website : -> http://www.turbocommons.org
 * License : -> Licensed under the Apache License, Version 2.0. You may not use this file except in compliance with the License.
 * License Url : -> http://www.apache.org/licenses/LICENSE-2.0
 * CopyRight : -> Copyright 2015 Edertone Advanded Solutions (08211 Castellar del Vallès, Barcelona). http://www.edertone.com
 */

namespace org\turbocommons\src\test\php\managers;

use org\turbocommons\src\main\php\managers\FilesManager;
use PHPUnit_Framework_TestCase;


/**
 * FilesManager tests
 *
 * @return void
 */
class FilesManagerTest extends PHPUnit_Framework_TestCase {


	/**
	 * testIsFile
	 *
	 * @return void
	 */
	public function testIsFile(){

		$filesManager = FilesManager::getInstance();

		// Create a temporary folder
		$basePath = $filesManager->createTempDirectory('TurboCommons-Php');
		$this->assertTrue($filesManager->isDirectoryEmpty($basePath));
		$this->assertTrue(!$filesManager->isFile($basePath));

		// Test with urls disabled
		$filesManager->acceptUrls = false;

		$this->assertTrue(!$filesManager->isFile(null));
		$this->assertTrue(!$filesManager->isFile(''));
		$this->assertTrue(!$filesManager->isFile('49568456'));
		$this->assertTrue(!$filesManager->isFile('http://www.adkgadsifi.com/ieriteroter3453458852t.pdf'));
		$this->assertTrue(!$filesManager->isFile('http://www.google.com'));
		$this->assertTrue(!$filesManager->isFile('https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js'));
		$this->assertTrue(!$filesManager->isFile('http://www.facebook.com'));

		// Test a created file
		$filesManager->createFile($basePath.DIRECTORY_SEPARATOR.'File.txt', 'Hello baby');
		$this->assertTrue($filesManager->isFile($basePath.DIRECTORY_SEPARATOR.'File.txt'));

		// Test with urls enabled
		$filesManager->acceptUrls = true;

		$this->assertTrue(!$filesManager->isFile(null));
		$this->assertTrue(!$filesManager->isFile(''));
		$this->assertTrue(!$filesManager->isFile('49568456'));
		$this->assertTrue(!$filesManager->isFile('http://www.adkgadsifi.com/ieriteroter3453458852t.pdf'));

		$this->assertTrue($filesManager->isFile($basePath.DIRECTORY_SEPARATOR.'File.txt'));
		$this->assertTrue($filesManager->isFile('https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js'), 'Could not load url. Internet connection must be available');
	}


	/**
	 * testIsDirectory
	 *
	 * @return void
	 */
	public function testIsDirectory(){

		$filesManager = FilesManager::getInstance();

		// Create a temporary folder
		$basePath = $filesManager->createTempDirectory('TurboCommons-Php');
		$this->assertTrue($filesManager->isDirectoryEmpty($basePath));
		$this->assertTrue($filesManager->isDirectory($basePath));

		// Test a non existing file
		$this->assertTrue(!$filesManager->isDirectory(null));
		$this->assertTrue(!$filesManager->isDirectory(''));
		$this->assertTrue(!$filesManager->isDirectory('49568456'));

		// Test a created file
		$filesManager->createFile($basePath.DIRECTORY_SEPARATOR.'File.txt', 'Hello baby');
		$this->assertTrue(!$filesManager->isDirectory($basePath.DIRECTORY_SEPARATOR.'File.txt'));
	}


	/**
	 * testIsDirectoryEmpty
	 *
	 * @return void
	 */
	public function testIsDirectoryEmpty(){

		$filesManager = FilesManager::getInstance();

		// Create a temporary folder
		$basePath = $filesManager->createTempDirectory('TurboCommons-Php');
		$this->assertTrue($filesManager->isDirectoryEmpty($basePath));

		// Create some file
		$filesManager->createFile($basePath.DIRECTORY_SEPARATOR.'File.txt', 'Hello baby');
		$this->assertTrue(!$filesManager->isDirectoryEmpty($basePath));

		// test that exception happens with non existant folder
		$this->setExpectedException('Exception');
		$filesManager->isDirectoryEmpty($basePath.DIRECTORY_SEPARATOR.'asdfwer');
	}


	/**
	 * testGetDirectorySeparator
	 *
	 * @return void
	 */
	public function testGetDirectorySeparator(){

		$filesManager = FilesManager::getInstance();

		$this->assertTrue($filesManager->getDirectorySeparator() === DIRECTORY_SEPARATOR);
	}


	/**
	 * testFindUniqueDirectoryName
	 *
	 * @return void
	 */
	public function testFindUniqueDirectoryName(){

		$filesManager = FilesManager::getInstance();

		// Create a temporary folder
		$basePath = $filesManager->createTempDirectory('TurboCommons-Php');
		$this->assertTrue($filesManager->isDirectoryEmpty($basePath));

		// Test generated directory names for the created empty folder
		$this->assertTrue($filesManager->findUniqueDirectoryName($basePath) == '1');
		$this->assertTrue($filesManager->findUniqueDirectoryName($basePath, 'NewFolder') == 'NewFolder');
		$this->assertTrue($filesManager->findUniqueDirectoryName($basePath, 'NewFolder', '-') == 'NewFolder');
		$this->assertTrue($filesManager->findUniqueDirectoryName($basePath, 'NewFolder', '-', true) == 'NewFolder');

		// Create some folders
		$filesManager->createDirectory($basePath.DIRECTORY_SEPARATOR.'1');
		$this->assertTrue($filesManager->isDirectory($basePath.DIRECTORY_SEPARATOR.'1'));
		$filesManager->createDirectory($basePath.DIRECTORY_SEPARATOR.'NewFolder');
		$this->assertTrue($filesManager->isDirectory($basePath.DIRECTORY_SEPARATOR.'NewFolder'));

		// Create a file that is named like a directory (without extension)
		$filesManager->createFile($basePath.DIRECTORY_SEPARATOR.'2', 'test file');
		$this->assertTrue($filesManager->isFile($basePath.DIRECTORY_SEPARATOR.'2'));

		// Verify generated dir names when folders already exist at destination path
		$this->assertTrue($filesManager->findUniqueDirectoryName($basePath) == '3');
		$this->assertTrue($filesManager->findUniqueDirectoryName($basePath, 'NewFolder') == 'NewFolder-1');
		$this->assertTrue($filesManager->findUniqueDirectoryName($basePath, 'NewFolder', '', '-', true) == '1-NewFolder');
		$this->assertTrue($filesManager->findUniqueDirectoryName($basePath, 'NewFolder', 'copy', '-', false) == 'NewFolder-copy-1');
		$this->assertTrue($filesManager->findUniqueDirectoryName($basePath, 'NewFolder', 'copy', '-', true) == 'copy-1-NewFolder');

		// Create some more folders
		$filesManager->createDirectory($basePath.DIRECTORY_SEPARATOR.'3');
		$this->assertTrue($filesManager->isDirectory($basePath.DIRECTORY_SEPARATOR.'3'));
		$filesManager->createDirectory($basePath.DIRECTORY_SEPARATOR.'NewFolder-1');
		$this->assertTrue($filesManager->isDirectory($basePath.DIRECTORY_SEPARATOR.'NewFolder-1'));
		$filesManager->createDirectory($basePath.DIRECTORY_SEPARATOR.'NewFolder-copy-1');
		$this->assertTrue($filesManager->isDirectory($basePath.DIRECTORY_SEPARATOR.'NewFolder-copy-1'));

		// Verify generated names again
		$this->assertTrue($filesManager->findUniqueDirectoryName($basePath) == '4');
		$this->assertTrue($filesManager->findUniqueDirectoryName($basePath, 'NewFolder') == 'NewFolder-2');
		$this->assertTrue($filesManager->findUniqueDirectoryName($basePath, 'NewFolder', '', '-', true) == '1-NewFolder');
		$this->assertTrue($filesManager->findUniqueDirectoryName($basePath, 'NewFolder', 'copy', '-', false) == 'NewFolder-copy-2');
		$this->assertTrue($filesManager->findUniqueDirectoryName($basePath, 'NewFolder', 'copy', '-', true) == 'copy-1-NewFolder');
	}


	/**
	 * testFindUniqueFileName
	 *
	 * @return void
	 */
	public function testFindUniqueFileName(){

		$filesManager = FilesManager::getInstance();

		// Create a temporary folder
		$basePath = $filesManager->createTempDirectory('TurboCommons-Php');
		$this->assertTrue($filesManager->isDirectoryEmpty($basePath));

		// Test generated file names for the created empty folder
		$this->assertTrue($filesManager->findUniqueFileName($basePath) == '1', 'error '.$filesManager->findUniqueFileName($basePath));
		$this->assertTrue($filesManager->findUniqueFileName($basePath, 'NewFile.txt') == 'NewFile.txt');
		$this->assertTrue($filesManager->findUniqueFileName($basePath, 'NewFile.txt', '-') == 'NewFile.txt');
		$this->assertTrue($filesManager->findUniqueFileName($basePath, 'NewFile.txt', '-', true) == 'NewFile.txt');

		// Create some files
		$filesManager->createFile($basePath.DIRECTORY_SEPARATOR.'1', 'hello baby');
		$this->assertTrue($filesManager->isFile($basePath.DIRECTORY_SEPARATOR.'1'));
		$filesManager->createFile($basePath.DIRECTORY_SEPARATOR.'NewFile.txt', 'hello baby');
		$this->assertTrue($filesManager->isFile($basePath.DIRECTORY_SEPARATOR.'NewFile.txt'));

		// Create a folder that is named like a possible file
		$filesManager->createDirectory($basePath.DIRECTORY_SEPARATOR.'2');
		$this->assertTrue($filesManager->isDirectory($basePath.DIRECTORY_SEPARATOR.'2'));

		// Verify generated file names when files already exist at destination path
		$this->assertTrue($filesManager->findUniqueFileName($basePath) == '3');
		$this->assertTrue($filesManager->findUniqueFileName($basePath, 'NewFile.txt') == 'NewFile-1.txt');
		$this->assertTrue($filesManager->findUniqueFileName($basePath, 'NewFile.txt', '', '-', true) == '1-NewFile.txt');
		$this->assertTrue($filesManager->findUniqueFileName($basePath, 'NewFile.txt', 'copy', '-', false) == 'NewFile-copy-1.txt');
		$this->assertTrue($filesManager->findUniqueFileName($basePath, 'NewFile.txt', 'copy', '-', true) == 'copy-1-NewFile.txt');

		// Create some more files
		$filesManager->createFile($basePath.DIRECTORY_SEPARATOR.'3', 'hello baby');
		$this->assertTrue($filesManager->isFile($basePath.DIRECTORY_SEPARATOR.'3'));
		$filesManager->createFile($basePath.DIRECTORY_SEPARATOR.'NewFile-1.txt', 'hello baby');
		$this->assertTrue($filesManager->isFile($basePath.DIRECTORY_SEPARATOR.'NewFile-1.txt'));
		$filesManager->createFile($basePath.DIRECTORY_SEPARATOR.'NewFile-copy-1.txt', 'hello baby');
		$this->assertTrue($filesManager->isFile($basePath.DIRECTORY_SEPARATOR.'NewFile-copy-1.txt'));

		// Verify generated names again
		$this->assertTrue($filesManager->findUniqueFileName($basePath) == '4');
		$this->assertTrue($filesManager->findUniqueFileName($basePath, 'NewFile.txt') == 'NewFile-2.txt');
		$this->assertTrue($filesManager->findUniqueFileName($basePath, 'NewFile.txt', '', '-', true) == '1-NewFile.txt');
		$this->assertTrue($filesManager->findUniqueFileName($basePath, 'NewFile.txt', 'copy', '-', false) == 'NewFile-copy-2.txt');
		$this->assertTrue($filesManager->findUniqueFileName($basePath, 'NewFile.txt', 'copy', '-', true) == 'copy-1-NewFile.txt');
	}
}

?>