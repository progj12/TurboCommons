<?php

/**
 * TurboCommons is a general purpose and cross-language library that implements frequently used and generic software development tasks.
 *
 * Website : -> http://www.turbocommons.org
 * License : -> Licensed under the Apache License, Version 2.0. You may not use this file except in compliance with the License.
 * License Url : -> http://www.apache.org/licenses/LICENSE-2.0
 * CopyRight : -> Copyright 2015 Edertone Advanded Solutions (08211 Castellar del Vallès, Barcelona). http://www.edertone.com
 */

namespace org\turbocommons\src\main\php\managers;

use org\turbocommons\src\main\php\model\BaseStrictClass;
use org\turbocommons\src\main\php\utils\StringUtils;
use org\turbocommons\src\main\php\utils\ObjectUtils;


/**
 * Class that allows us to manage validation in an encapsulated way.
 * We can create as many instances as we want, and each instance will store the validation history and global validation state,
 * so we can use this class to validate complex forms or multiple elements globally
 */
class ValidationManager extends BaseStrictClass{


	/**
	 * Constant that defines the correct validation status
	 */
	const VALIDATION_OK = 0;


	/**
	 * Constant that defines the warning validation status
	 */
	const VALIDATION_WARNING = 1;


	/**
	 * Constant that defines the error validation status
	 */
	const VALIDATION_ERROR = 2;


	/** Stores the current state for the applied validations (VALIDATION_OK / VALIDATION_WARNING / VALIDATION_ERROR) */
	public $validationStatus = 0;


	/** Stores the list of generated warning or error messages, in the same order as happened. */
	public $failedMessagesList = [];


	/** Stores the list of failure status codes, in the same order as happened. */
	public $failedStatusList = [];


	/** Stores the last error message generated by a validation error / warning or empty string if no validation errors happened */
	public $lastMessage = '';


	/**
	 * Validation will fail if specified value is not a true boolean value
	 *
	 * @param boolean $value A boolean expression to validate
	 * @param string $errorMessage The error message that will be generated if validation fails
	 * @param boolean $isWarning Tells if the validation fail will be processed as a validation error or a validation warning
	 *
	 * @return boolean False in case the validation fails or true if validation succeeds.
	 */
	public function isTrue($value, $errorMessage = '', $isWarning = false){

		// Set optional parameters default values
		$errorMessage = (StringUtils::isEmpty($errorMessage)) ? 'value is not true' : $errorMessage;

		$res = (!$value) ? $errorMessage : '';

		return $this->_updateValidationStatus($res, $isWarning);
	}


	/**
	 * Validation will fail if specified value is not a boolean
	 *
	 * @param boolean $value The boolean to validate
	 * @param string $errorMessage The error message that will be generated if validation fails
	 * @param boolean $isWarning Tells if the validation fail will be processed as a validation error or a validation warning
	 *
	 * @return boolean False in case the validation fails or true if validation succeeds.
	 */
	public function isBoolean($value, $errorMessage = '', $isWarning = false){

		// Set optional parameters default values
		$errorMessage = (StringUtils::isEmpty($errorMessage)) ? 'value is not a boolean' : $errorMessage;

		$res = !is_bool($value) ? $errorMessage : '';

		return $this->_updateValidationStatus($res, $isWarning);
	}


	/**
	 * Validation will fail if specified value is not numeric
	 *
	 * @param Number $value The number to validate
	 * @param string $errorMessage The error message that will be generated if validation fails
	 * @param boolean $isWarning Tells if the validation fail will be processed as a validation error or a validation warning
	 *
	 * @return boolean False in case the validation fails or true if validation succeeds.
	 */
	public function isNumeric($value, $errorMessage = '', $isWarning = false){

		// Set optional parameters default values
		$errorMessage = (StringUtils::isEmpty($errorMessage)) ? 'value is not a number' : $errorMessage;

		$res = (!is_numeric($value)) ? $errorMessage : '';

		return $this->_updateValidationStatus($res, $isWarning);
	}


	/**
	 * Validation will fail if specified value is not a string
	 *
	 * @param string $value The element to validate
	 * @param string $errorMessage The error message that will be generated if validation fails
	 * @param boolean $isWarning Tells if the validation fail will be processed as a validation error or a validation warning
	 *
	 * @returns boolean False in case the validation fails or true if validation succeeds.
	 */
	public function isString($value, $errorMessage = '', $isWarning = false){

		// Set optional parameters default values
		$errorMessage = (StringUtils::isEmpty($errorMessage)) ? 'value is not a string' : $errorMessage;

		$res = (!is_string($value)) ? $errorMessage : '';

		return $this->_updateValidationStatus($res, $isWarning);
	}


	/**
	 * TODO - translate from js
	 */
	public function isUrl($value, $errorMessage = '', $isWarning = false){

		// Set optional parameters default values
		$errorMessage = (StringUtils::isEmpty($errorMessage)) ? 'value is not an URL' : $errorMessage;

		$res = $errorMessage;

		$validationManager = new ValidationManager();

		if($validationManager->isFilledIn($value) && $validationManager->isString($value)){

			// This amazingly good solution's been found at https://jkwl.io/php/regex/2015/05/18/url-validation-php-regex.html
			$urlRegex = "#^" .
			        // protocol identifier
			        "(?:(?:https?|ftp):\\/\\/)?" .
			        // user:pass authentication
			        "(?:\\S+(?::\\S*)?@)?" .
			        "(?:" .
			        // IP address exclusion
			        // private & local networks
			        "(?!(?:10|127)(?:\\.\\d{1,3}){3})" .
			        "(?!(?:169\\.254|192\\.168)(?:\\.\\d{1,3}){2})" .
			        "(?!172\\.(?:1[6-9]|2\\d|3[0-1])(?:\\.\\d{1,3}){2})" .
			        // IP address dotted notation octets
			        // excludes loopback network 0.0.0.0
			        // excludes reserved space >= 224.0.0.0
			        // excludes network & broacast addresses
			        // (first & last IP address of each class)
			        "(?:[1-9]\\d?|1\\d\\d|2[01]\\d|22[0-3])" .
			        "(?:\\.(?:1?\\d{1,2}|2[0-4]\\d|25[0-5])){2}" .
			        "(?:\\.(?:[1-9]\\d?|1\\d\\d|2[0-4]\\d|25[0-4]))" .
			        "|" .
			        // host name
			        "(?:(?:[a-z\\x{00a1}-\\x{ffff}0-9]-*)*[a-z\\x{00a1}-\\x{ffff}0-9]+)" .
			        // domain name
			        "(?:\\.(?:[a-z\\x{00a1}-\\x{ffff}0-9]-*)*[a-z\\x{00a1}-\\x{ffff}0-9]+)*" .
			        // TLD identifier
			        "(?:\\.(?:[a-z\\x{00a1}-\\x{ffff}]{2,}))" .
			        ")" .
			        // port number
			        "(?::\\d{2,5})?" .
			        // resource path
			        "(?:\\/\\S*)?" .
			        "$#ui";

			$res = !(strlen($value) < 2083 && preg_match($urlRegex, $value)) ? $errorMessage : '';
		}

		return $this->_updateValidationStatus($res, $isWarning);
	}


	/**
	 * Validation will fail if specified value is not an array
	 *
	 * @param array value The array to validate
	 * @param string errorMessage The error message that will be generated if validation fails
	 * @param boolean isWarning Tells if the validation fail will be processed as a validation error or a validation warning
	 *
	 * @return boolean False in case the validation fails or true if validation succeeds.
	 */
	public function isArray($value, $errorMessage = '', $isWarning = false){

		// Set optional parameters default values
		$errorMessage = (StringUtils::isEmpty($errorMessage)) ? 'value is not an array' : $errorMessage;

		$res = (!is_array($value)) ? $errorMessage : '';

		return $this->_updateValidationStatus($res, $isWarning);
	}


	/**
	 * Validation will fail if specified value is not an object
	 *
	 * @param object $value The object to validate
	 * @param string $errorMessage The error message that will be generated if validation fails
	 * @param boolean $isWarning Tells if the validation fail will be processed as a validation error or a validation warning
	 *
	 * @return boolean False in case the validation fails or true if validation succeeds.
	 */
	public function isObject($value, $errorMessage = '', $isWarning = false){

		// Set optional parameters default values
		$errorMessage = (StringUtils::isEmpty($errorMessage)) ? 'value is not an object' : $errorMessage;

		$res = ($value == null || !is_object($value)) ? $errorMessage : '';

		return $this->_updateValidationStatus($res, $isWarning);
	}


	/**
	 * Validation will fail if specified text is empty.<br>
	 * See Stringutils.isEmpty to understand what is considered as an empty text
	 *
	 * @see Stringutils::isEmpty
	 *
	 * @param string $value A text that must not be empty.
	 * @param array $emptyChars Optional array containing a list of string values that will be considered as empty for the given string. This can be useful in some cases when we want to consider a string like 'NULL' as an empty string.
	 * @param string $errorMessage The error message that will be generated if validation fails
	 * @param boolean $isWarning Tells if the validation fail will be processed as a validation error or a validation warning
	 *
	 * @return boolean False in case the validation fails or true if validation succeeds.
	 */
	public function isFilledIn($value, $emptyChars = [], $errorMessage = '', $isWarning = false){

		// Set optional parameters default values
		$errorMessage = (StringUtils::isEmpty($errorMessage)) ? 'value is required' : $errorMessage;

		$res = StringUtils::isEmpty($value, $emptyChars) ? $errorMessage : '';

		return $this->_updateValidationStatus($res, $isWarning);
	}


	/**
	 * TODO - translate from JS
	 */
	public function isDate(){

		// TODO - translate from JS
	}


	/**
	 * TODO - translate from JS
	 */
	public function isMail(){

		// TODO - translate from JS
	}


	/**
	 * TODO - translate from JS
	 */
	public function isEqualTo(){

		// TODO - translate from JS
	}


	/**
	 * TODO - translate from JS
	 */
	public function isMinimumWords(){

		// TODO - translate from JS
	}


	/**
	 * TODO - translate from JS
	 */
	public function isNIF(){

		// TODO - translate from JS
	}


	/**
	 * TODO - translate from JS
	 */
	public function isMinimumLength(){

		// TODO - translate from JS
	}


	/**
	 * TODO - translate from JS
	 */
	public function isPostalCode(){

		// TODO - translate from JS
	}


	/**
	 * TODO - translate from JS
	 */
	public function isPhone(){

		// TODO - translate from JS
	}


	/**
	 * TODO - translate from JS
	 */
	public function isHtmlFormValid(){

		// TODO - translate from JS
	}


	/**
	 * Reinitialize the validation status for this class
	 *
	 * @return void
	 */
	public function reset(){

		$this->validationStatus = self::VALIDATION_OK;
		$this->failedMessagesList = [];
		$this->failedStatusList = [];
		$this->lastMessage = '';
	}


	/**
	 * Update the class validation Status depending on the provided error message.
	 *
	 * @param string $errorMessage The error message that's been generated from a previously executed validation method
	 * @param boolean $isWarning Tells if the validation fail will be processed as a validation error or a validation warning
	 *
	 * @return boolean True if received errorMessage was '' (validation passed) or false if some error message was received (validation failed)
	 */
	private function _updateValidationStatus($errorMessage, $isWarning){

		// If we are currently in an error state, nothing to do
		if($this->validationStatus == self::VALIDATION_ERROR){

			return $errorMessage == '';
		}

		// If the validation fails, we must change the validation status
		if($errorMessage != ""){

			array_push($this->failedMessagesList, $errorMessage);

			if($isWarning){

				array_push($this->failedStatusList, self::VALIDATION_WARNING);
				$this->lastMessage = $errorMessage;

			}else{

				array_push($this->failedStatusList, self::VALIDATION_ERROR);
				$this->lastMessage = $errorMessage;
			}

			if($isWarning && $this->validationStatus != self::VALIDATION_ERROR){

				$this->validationStatus = self::VALIDATION_WARNING;

			}else{

				$this->validationStatus = self::VALIDATION_ERROR;
			}
		}

		return $errorMessage == '';
	}
}

?>