"use strict";

/**
 * TurboCommons is a general purpose and cross-language library that implements frequently used and generic software development tasks.
 *
 * License : -> Licensed under the Apache License, Version 2.0. You may not use this file except in compliance with the License.
 * License Url : -> http://www.apache.org/licenses/LICENSE-2.0
 * CopyRight : -> Copyright 2015 Edertone Advanded Solutions (08211 Castellar del Vallès, Barcelona). http://www.edertone.com
 */


/* exported ConversionUtils */

/**
 * Static class with the most common conversion utilities to change the data from a simple type to another one.
 * To convert complex classes or structures, use SerializationUtils class.
 * 
 * import path: 'js/libs/libEdertoneJS/utils/ConversionUtils.js'
 */
var ConversionUtils = {


	/**
	 * Encode an string to base64
	 * 
	 * Found at: http://www.webtoolkit.info/
	 * 			 http://www.webtoolkit.info/javascript-base64.html#.VO3gzjSG9AY
	 * 
	 * @param input The input string to be converted
	 * @returns The base64 string
	 */
	stringToBase64 : function(input){

		var keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";

		var output = "";
		var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
		var i = 0;

		// Auxiliary method to encode a string as utf 8
		function utf8Encode(string){

			string = string.replace(/\r\n/g, "\n");
			var utftext = "";

			for(var n = 0; n < string.length; n++){

				var c = string.charCodeAt(n);

				if(c < 128){
					utftext += String.fromCharCode(c);
				}else if((c > 127) && (c < 2048)){
					utftext += String.fromCharCode((c >> 6) | 192);
					utftext += String.fromCharCode((c & 63) | 128);
				}else{
					utftext += String.fromCharCode((c >> 12) | 224);
					utftext += String.fromCharCode(((c >> 6) & 63) | 128);
					utftext += String.fromCharCode((c & 63) | 128);
				}
			}

			return utftext;
		}

		input = utf8Encode(input);

		while(i < input.length){

			chr1 = input.charCodeAt(i++);
			chr2 = input.charCodeAt(i++);
			chr3 = input.charCodeAt(i++);

			enc1 = chr1 >> 2;
			enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
			enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
			enc4 = chr3 & 63;

			if(isNaN(chr2)){

				enc3 = enc4 = 64;

			}else if(isNaN(chr3)){

				enc4 = 64;
			}

			output = output + keyStr.charAt(enc1) + keyStr.charAt(enc2) + keyStr.charAt(enc3) + keyStr.charAt(enc4);
		}

		return output;

	},


	/**
	 * Decode an string from base64
	 * 
	 * Found at: http://www.webtoolkit.info/
	 * 			 http://www.webtoolkit.info/javascript-base64.html#.VO3gzjSG9AY
	 * 
	 * @param input The base64 string
	 * 
	 * @returns The base64 decoded string
	 */
	base64ToString : function(input){

		var keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";

		var output = "";
		var chr1, chr2, chr3;
		var enc1, enc2, enc3, enc4;
		var i = 0;

		input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

		while(i < input.length){

			enc1 = keyStr.indexOf(input.charAt(i++));
			enc2 = keyStr.indexOf(input.charAt(i++));
			enc3 = keyStr.indexOf(input.charAt(i++));
			enc4 = keyStr.indexOf(input.charAt(i++));

			chr1 = (enc1 << 2) | (enc2 >> 4);
			chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
			chr3 = ((enc3 & 3) << 6) | enc4;

			output = output + String.fromCharCode(chr1);

			if(enc3 != 64){

				output = output + String.fromCharCode(chr2);
			}

			if(enc4 != 64){

				output = output + String.fromCharCode(chr3);
			}
		}

		// Auxiliary method for utf8 decoding
		function utf8Decode(utftext){

			var string = "";
			var i = 0;
			var c = 0;
			var c2 = 0;

			while(i < utftext.length){

				c = utftext.charCodeAt(i);

				if(c < 128){

					string += String.fromCharCode(c);
					i++;

				}else if((c > 191) && (c < 224)){

					c2 = utftext.charCodeAt(i + 1);
					string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
					i += 2;

				}else{

					c2 = utftext.charCodeAt(i + 1);
					c3 = utftext.charCodeAt(i + 2);
					string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
					i += 3;
				}
			}

			return string;
		}

		output = utf8Decode(output);

		return output;
	}
};