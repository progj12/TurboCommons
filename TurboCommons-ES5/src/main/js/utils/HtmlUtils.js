"use strict";

/**
 * TurboCommons is a general purpose and cross-language library that implements frequently used and generic software development tasks.
 *
 * Website : -> http://www.turbocommons.org
 * License : -> Licensed under the Apache License, Version 2.0. You may not use this file except in compliance with the License.
 * License Url : -> http://www.apache.org/licenses/LICENSE-2.0
 * CopyRight : -> Copyright 2015 Edertone Advanded Solutions (08211 Castellar del Vallès, Barcelona). http://www.edertone.com
 */

/** @namespace */
var org_turbocommons_src_main_js_utils = org_turbocommons_src_main_js_utils || {};


/**
 * Utilities related with manipulating HTML elements
 * 
 * <pre><code> 
 * This is a static class, so no instance needs to be created.
 * Usage example:
 * 
 * var ns = org_turbocommons_src_main_js_utils;
 * 
 * var ids = ns.HtmlUtils.findDuplicateIds();
 * 
 * var id = ns.HtmlUtils.generateUniqueId(element);
 * ...
 * </code></pre>
 * 
 * @requires jQuery
 * @class
 */
org_turbocommons_src_main_js_utils.HtmlUtils = {


	/**
	 * Check if the specified id exists on the current document.  
	 * 
	 * @static
	 * 
	 * @param {string} html identifier we want to find on the current document
	 * 
	 * @returns {boolean} true if the specified id already exists on the current page or false if not. 
	 */
	idExists : function(id){

		return $("#" + id).length > 0;
	},


	/**
	 * Looks on the current html document to check if any duplicate element exists. In case any duplicate id is found, an error will be raised  
	 * 
	 * @static
	 * 
	 * @returns {boolean} False if no duplicate ids where found on the current page. If a duplicate id is found, an exception will be thrown showing the duplicated id name
	 */
	findDuplicateIds : function(){

		$('[id]').each(function(){

			var ids = $('[id="' + this.id + '"]');

			if(ids.length > 1 && ids[0] == this){

				throw new Error("HtmlUtils.findDuplicateIds - Duplicate id found on Html document: " + this.id);
			}

		});

		return false;
	},


	/**
	 * Creates a unique id value that can be used on an HTML element, totally colision safe. This means no other html element on the current document will have the same id as the generated one. 
	 * 
	 * @static
	 * 
	 * @param {string} prefix If we want to give a meaningful prefix to the generated id, so it is easier to identify it on the source code, we can set it here. The result will be something like `prefix-XXXXXXXXXX'
	 *
	 * @returns {boolean} A generated id in the form id-XXXXX (if no prefix specified) or prefix-XXXXXX that is verified to not exist on any other part of the document.
	 */
	generateUniqueId : function(prefix){

		// Set default values if they are not defined
		prefix = prefix === undefined ? 'id' : prefix;

		// Generate the random id and verify that it does not exist
		do{

			var id = prefix + '-' + Math.random().toString(36).substr(2, 16);

		}while($("#" + id).length > 0);

		return id;
	},


	/**
	 * Replace all the occurences of a string with the given text, directly inside the contents of the specified page element.
	 * 
	 * @static
	 *  
	 * @param {string} search The text we are searching to be replaced
	 * @param {string} replace The text that will replace the search text if found
	 * @param {object} element $("body") by default. A jquery object representing the page element where replacement will be performed. If not specified replacement will be performed on all the current html document.
	 *
	 * @returns void
	 */
	replaceTextOnElement : function(search, replace, element){

		if(search === undefined){

			throw new Error("HtmlUtils.replaceTextOnElement - Text to be replaced is undefined");
		}

		if(replace === undefined){

			throw new Error("HtmlUtils.replaceTextOnElement - Replacement text is undefined");
		}

		// Set optional parameters default values
		element = (element === undefined) ? $("body") : element;

		element.html(element.html().replace(search, replace));
	},


	/**
	 * Totally disables a form and all of its elements
	 * 
	 * @static
	 * 
	 * @param {object} form A jquery object representing the form to disable. We can pass an htm form element, or also a div containing inputs, buttons, and so.
	 *
	 * @returns void
	 */
	disableForm : function(form){

		// Apply a little alpha
		form.css('opacity', 0.7);

		// Disable all the form elements
		form.find(":input").attr('disabled', true);
		form.find(":submit").attr('disabled', true);
	},


	/**
	 * Re enable a form previously disabled with disableForm method
	 * 
	 * @static
	 * 
	 * @param {object} form A jquery object representing the form to enable. We can pass an htm form element, or also a div containing inputs, buttons, and so.
	 * 
	 * @returns void
	 */
	enableForm : function(form){

		// Restore form opacity
		form.css('opacity', '');

		// Enable all the form elements again
		form.find(":input").attr('disabled', false);
		form.find(":submit").attr('disabled', false);
	},


	/**
	 * Clears all the inputs, textareas, etc, on the specified form.
	 * 
	 * @static
	 * 
	 * @param {object} form A jquery object representing the form to clear. We can pass an htm form element, or also a div containing inputs, buttons, and so.
	 * 
	 * @returns void
	 */
	clearForm : function(form){

		form.find(":text").each(function(){

			$(this).val("");

		});

		form.find(":password").each(function(){

			$(this).val("");

		});
	},


	/**
	 * Send the data contained on the specified form to the specified remote url. This method is normally used on the action="" attribute on an html form, but we can use it with any html container that includes form elements like inputs and so.<br>
	 * This is the usual definition for a form that uses this method:<br><br>
	 * 
	 *  &lt;form id="form"<br>
			method="POST"<br>
			data-validationError="&lt;?php echo LOC_VALIDATION_PLEASE_REVIEW_FORM ?&gt;"<br>
			data-formSentMessage="&lt;?php echo LOC_VALIDATION_THANKS_FOR_CONTACTING ?&gt;"<br>
			data-formSendErrorMessage="&lt;?php echo LOC_VALIDATION_ERROR_SENDING_FORM_TRY_AGAIN ?&gt;"<br>
			onsubmit="return (new ValidationManager()).isHtmlFormValid($('#form'))"<br>
			action="javascript:HtmlUtils.submitForm($('#form'), '&lt;?php App::echoUrl('php/http/ContactFormSend.php') ?&gt;')"&gt;<br><br>

			&lt;p&gt;&lt;?php echo LOC_FORMS_NAME_SURNAMES ?&gt;:&lt;/p&gt;<br>
			&lt;input id="name" type="text" data-validationType="required minLen-3" /&gt;<br><br>

			&lt;p&gt;&lt;?php echo LOC_FORMS_MAIL ?&gt;:&lt;/p&gt;<br>
			&lt;input id="mail" type="text" data-validationType="required mail" /&gt;<br><br>

			&lt;p&gt;&lt;?php echo LOC_FORMS_PHONE ?&gt;:&lt;/p&gt;<br>
			&lt;input id="phone" type="text" data-validationType="required phone" /&gt;<br><br>

			&lt;p&gt;&lt;?php echo LOC_FORMS_MESSAGE ?&gt;:&lt;/p&gt;<br>
			&lt;textarea id="message" data-validationType="required minLen-5"&gt;&lt;/textarea&gt;<br><br>

			&lt;input id="submit" type="submit" value="&lt;?php echo LOC_FORMS_SEND ?&gt;" /&gt;<br>

	 *	&lt;/form&gt;
	 * 
	 * @static
	 * 
	 * @param {object} form A jquery object representing the html form to submit, or any other html container with form elements inside it
	 * @param {string} url The url that will process the form sending. Response for this url must use the standard server result structure, where state == '0' means sending was ok.
	 * @param {string} sentAction Tells what operation must be done after sending is ok. Can have one of the following values:<br>
	 * &emsp;&emsp;'' means we will look for the data-formSentMessage attribute on the form element. If attribute contains text, an alert will be shown with its contents. Otherwise, a generic text will be shown<br>
	 * &emsp;&emsp;'Some text' value will be shown as an alert<br>
	 * &emsp;&emsp;'A function' will be called after successful sending. Url response data will be passed to this method as an object
	 * @param {string} errorAction The action to execute when the form sending or http request failed. Format is exactly the same as sentAction (using data-formSendErrorMessage attribute on the form element)
	 * @param {boolean} reset True by default. If enabled, the form will be reset after being correctly sent.
	 * 
	 * @returns void
	 */
	submitForm : function(form, url, sentAction, errorAction, reset){

		// Set optional parameters default values
		sentAction = (sentAction === undefined) ? form.attr("data-formSentMessage") : sentAction;
		errorAction = (errorAction === undefined) ? form.attr("data-formSendErrorMessage") : errorAction;
		reset = (reset === undefined) ? true : reset;

		// Set the action default texts if nothing could be retrieved
		if(sentAction == '' || sentAction === undefined){

			sentAction = 'Form ok';
		}

		if(errorAction == '' || errorAction === undefined){

			errorAction = 'Form Error';
		}

		// Set a wait cursor for the body
		var bodyCursor = $("body").css('cursor');

		$("body").css('cursor', 'wait');

		// Disable all the form elements
		HtmlUtils.disableForm(form);

		// If the specified element is an HTML form, we will use its defined method. Otherwise we will use post.
		var method = (form.is("form")) ? form.attr('method') : 'POST';

		// Perform the http request to the destination url
		$.ajax({
			type : method,
			url : url,
			data : SerializationUtils.formToObject(form),
			success : function(data){

				// Restore the body cursor state
				$("body").css('cursor', bodyCursor);

				data = SerializationUtils.xmlToObject(data);

				var action = errorAction;

				if(data.state == '0'){

					action = sentAction;

					if(reset){

						if((form.is("form"))){

							form[0].reset();

						}else{

							HtmlUtils.clearForm(form);
						}
					}
				}

				// Execute the sent action depending on if it's a string or a method
				if(typeof action == 'string' || action instanceof String){

					alert(action);
				}

				if(typeof action == "function"){

					action.apply(data);
				}

				HtmlUtils.enableForm(form);
			},
			error : function(data){

				// Restore the body cursor state
				$("body").css('cursor', bodyCursor);

				// Execute the error action depending on if it's a string or a method
				if(typeof errorAction == 'string' || errorAction instanceof String){

					alert(errorAction);
				}

				if(typeof errorAction == "function"){

					errorAction.apply(SerializationUtils.xmlToObject(data));
				}

				HtmlUtils.enableForm(form);
			}
		});
	},
};