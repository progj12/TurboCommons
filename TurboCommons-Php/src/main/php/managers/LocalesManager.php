<?php

/**
 * TurboCommons is a general purpose and cross-language library that implements frequently used and generic software development tasks.
 *
 * License : -> Licensed under the Apache License, Version 2.0. You may not use this file except in compliance with the License.
 * License Url : -> http://www.apache.org/licenses/LICENSE-2.0
 * CopyRight : -> Copyright 2015 Edertone Advanded Solutions (08211 Castellar del Vallès, Barcelona). http://www.edertone.com
 */

namespace com\edertone\turboCommons\src\main\php\managers;

use Exception;
use com\edertone\turboCommons\src\main\php\model\BaseSingletonClass;
use com\edertone\turboCommons\src\main\php\utils\FileSystemUtils;
use com\edertone\turboCommons\src\main\php\utils\StringUtils;
use com\edertone\turboCommons\src\main\php\utils\SerializationUtils;


/**
 * A class that is used to manage the internationalization for our application texts.<br>
 * Main features in brief:<br><br>
 * - Loads resource bundles from one or more specified paths, by order of preference<br>
 * - Supports several resourcebundle formats<br>
 * - A list of locales can be specified so the class will load them by order of preference if any tag is missing.<br>
 * - Supports diferent folder structures for the resourcebundles organization.<br>
 * - Uses a lazy method to load only the requested bundles and tries to minimize disk requests.
 */
class LocalesManager extends BaseSingletonClass{


	/**
	 * A list of languages by order of preference to search in the available resource bundles for a translation<br><br>
	 * For example: Setting this property to ['en_US', 'es_ES', 'fr_FR'] and calling
	 * LocalesManager::getInstance()->get('HELLO', 'Greetings') will try to locate the en_US value for the
	 * HELLO tag on the Greetings bundle. If the tag is not found for the specified locale and bundle, the same
	 * operation will be performed for the es_ES locale, and so, till a value is found or no more locales are defined.
	 */
	public $locales = [];


	/**
	 * Specifies the expected format for the loaded resourcebundle files.
	 * Possible values: 'properties'
	 *
	 * TODO: Add more formats
	 */
	public $bundleFormat = 'properties';


	/**
	 * List of filesystem paths (relative or absolute) where the resourcebundles are located.
	 * The class will try to load the data from the paths in order of preference, so if a duplicate key is found,
	 * the one read first will prevail.
	 *
	 * Example: ['../locales', 'src/resources/shared/locales']
	 */
	public $paths = [];


	/**
	 * Defines the expected folder structure for the folders that contain the resourcebundles.
	 * When trying to locate the bundle files, $locale will be replaced by the expected locale, and $bundle by the bundle name.
	 *
	 * TODO: improve documentation
	 */
	public $pathStructure = '$locale/$bundle.properties';


	/**
	 * Stores the locales data as it is read from disk
	 */
	private $_loadedData = [];


	/** Stores the latest resource bundle that's been used to read a localized value */
	private $_lastBundle = '';


	/**
	 * Returns the global LocalesManager singleton instance.
	 *
	 * @return LocalesManager The Locales Manager instance.
	 */
	public static function getInstance(){

		// This method is overriden from the singleton one simply to get correct
		// autocomplete annotations when returning the instance
		$instance = parent::getInstance();

		return $instance;
	}


	/**
	 * Reads the value for the specified bundle, key and locale.
	 *
	 * @param string $key The key we want to read from the specified resource bundle
	 * @param string $bundle The name for the resource bundle file. If not specified, the value
	 * that was used on the inmediate previous call of this method will be used. This can save us lots of typing
	 * if we are reading multiple consecutive keys from the same bundle.
	 * @param string $locale The locale we are requesting from the specified bundle and key. If not specified, the value
	 * that is defined on the locale parameter of this class will be used.
	 *
	 * @return string The localized text
	 */
	public function get($key, $bundle = '', $locale = ''){

		// Locales must be an array
		if(!is_array($this->locales)){

			throw new Exception('LocalesManager->get: locales property must be an array');
		}

		// Check if we need to load the last used bundle
		if($bundle == ''){

			$bundle = $this->_lastBundle;
		}

		if($bundle == ''){

			throw new Exception('LocalesManager->get: No resource bundle specified');
		}

		// Add the specified locale at the start of the list of locales
		if($locale != ''){

			array_unshift($this->locales, $locale);
		}

		// Loop all the locales to find the first one with a value for the specified key
		foreach ($this->locales as $locale) {

			// Check if we need to load the bundle from disk
			if(!isset($this->_loadedData[$bundle][$locale])){

				$this->_loadBundle($bundle, $locale);
			}

			if(isset($this->_loadedData[$bundle][$locale][$key])){

				return $this->_loadedData[$bundle][$locale][$key];
			}
		}

		throw new Exception('LocalesManager->get: Specified key <'.$key.'> was not found on locales list: ['.implode(', ', $this->locales).']');
	}


	/**
	 * Read the specified bundle and locale from disk and store the values on memory
	 *
	 * @param string $bundle The name for the bundle we want to load
	 * @param string $locale The specific language we want to load
	 *
	 * @return void
	 */
	private function _loadBundle($bundle, $locale){

		// Process the path format string
		$pathStructure = str_replace(['$bundle', '$locale'], [$bundle, $locale], $this->pathStructure);

		foreach ($this->paths as $path) {

			$bundlePath = StringUtils::formatPath($path.DIRECTORY_SEPARATOR.$pathStructure);

			if(FileSystemUtils::isFile($bundlePath)){

				$bundleData = FileSystemUtils::readFile($bundlePath);

				$this->_loadedData[$bundle][$locale] = SerializationUtils::propertiesToArray($bundleData);

				return;
			}
		}

		throw new Exception('LocalesManager->_loadBundle: Could not load bundle <'.$bundle.'> and locale <'.$locale.'>');
	}
}

?>