<?php

/**
 * TurboCommons-Php
 *
 * PHP Version 5.4
 *
 * @copyright 2015 Edertone advanced solutions (http://www.edertone.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://turbocommons.org
 */


namespace com\edertone\turboCommons\src\main\php\utils;


/**
 * This class contains a collection of methods that are related to internet browsers<br><br>
 * import path: ProjectPaths::LIBS_EDERTONE_PHP.'/utils'
 */
class BrowserUtils{


	/**
	 * Reloads the current url. Note that any POST data will be lost by performing this operation.
	 * Important: No output must be generated by PHP prior to calling this method, or headers will fail.
	 *
	 * @return void
	 */
	public static function reloadPage(){

		header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		die();
	}


	/**
	 * Redirects the browser to the specified page. Note that as this method uses headers, no output can be written before performing the redirect or it will fail.
	 *
	 * @param string $url The url where the browser will be redirected.
	 * @param boolean $movedPermanently True tells that the redirection mode must be set as moved permanently (301), false means temporary (302). True by default.
	 *
	 * @return void
	 */
	public static function goToUrl($url, $movedPermanently = true){

		if($movedPermanently){

			header('HTTP/1.1 301 Moved Permanently');
		}

		header('location:'.$url);

		die();
	}


	/**
	 * Launch a 404 error and include the project 404 error page. Note that this method uses headers, so if some output has been written, the error won't work.
	 * This method will end the current execution with a die() call.
	 *
	 * @param string $includeErrorDoc Tells the method to include the error doc as part of the 404 error generation. True by default.
	 * @param string $errorDocBasePath Use this to set the path where the error document is located on the project. If not specified, the default one will be used (Error404.php placed on the root of the project)
	 *
	 * @return void
	 */
	public static function launch404Error($includeErrorDoc = true, $errorDocBasePath = ''){

		header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');

		if($includeErrorDoc){

			if($errorDocBasePath == ''){

				if(is_file(__DIR__.'/../../../../Error404.php')){

					$errorDocBasePath = (__DIR__.'/../../../../');

				}else{

					$errorDocBasePath = (__DIR__.'/../../../../../');
				}

			}

			include_once $errorDocBasePath.'Error404.php';
		}

		die();
	}


	/**
	 * Method that tries to detect if the browser that is loading the current script is a bot. It will try to find on the browser agent some common texts that are related to bots.
	 * Note that we cannot guarantee that a false result means that the browser is not a bot, but we mainly can be sure that a true value means the browser is a bot.<br><br>
	 *
	 * For a list of updated browser user agents, go to http://www.user-agents.org/
	 *
	 * @return boolean True if the browser is detected as a bot false instead.
	 */
	public static function isABot(){

		if(!isset($_SERVER['HTTP_USER_AGENT'])){

			return false;
		}

		$bots = array('bot', 'crawl', 'slurp', 'facebookexternalhit', 'spider', 'Feedfetcher-Google');

		if(preg_match('/'.implode('|', $bots).'/i', $_SERVER['HTTP_USER_AGENT'])) {

			return true;

		}else {

			return false;
		}
	}

}

?>