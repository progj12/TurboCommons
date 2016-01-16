<?php

/**
 * TurboCommons is a general purpose and cross-language library that implements frequently used and generic software development tasks.
 *
 * License : -> Licensed under the Apache License, Version 2.0. You may not use this file except in compliance with the License.
 * License Url : -> http://www.apache.org/licenses/LICENSE-2.0
 * CopyRight : -> Copyright 2015 Edertone Advanded Solutions (08211 Castellar del Vallès, Barcelona). http://www.edertone.com
 */

require_once __DIR__.'/../main/php/AutoLoader.php';
require_once __DIR__.'/php/libs/phpunit.phar';


$phpunit = new PHPUnit_TextUI_TestRunner();

// Run all the tests inside the current folder or subfolders for all the files ending with Test.php
if($phpunit->dorun($phpunit->getTest(__DIR__, '', 'Test.php'))->failureCount() > 0){

	throw new Exception();
}

?>