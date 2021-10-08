/*(function( $ ) {
	'use strict';

	/!**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 *!/

})( jQuery );*/



var queryForm = function(settings){
	var reset = settings && settings.reset ? settings.reset : false;
	var self = window.location.toString();
	var querystring = self.split("?");
	if (querystring.length > 1) {
		var pairs = querystring[1].split("&");
		for (i in pairs) {
			var keyval = pairs[i].split("=");
			if (reset || localStorage.getItem(keyval[0]) === null) {
				localStorage.setItem(keyval[0], decodeURIComponent(keyval[1]));
			}
		}
	}
	var hiddenFields = document.querySelectorAll("input[type=hidden], input[type=text]");
	for (var i=0; i<hiddenFields.length; i++) {
		var param = localStorage.getItem(hiddenFields[i].name);
		if (param) document.getElementsByName(hiddenFields[i].name)[0].value = param;
	}
}

setTimeout(function(){queryForm();}, 3000);




function woocommerceUtmGetLocalStorageVars(){

	if( undefined === window.localStorage.getItem('_dataLayerHistory') ){
		return false;
	}

	let dataLayerHistory = JSON.parse( window.localStorage.getItem('_dataLayerHistory') );

	return dataLayerHistory ?? false;

}

function woocommerceUtmMaybeGetUtmSourceFromLocalStorage(){

	let localStorageVars = woocommerceUtmGetLocalStorageVars();
	if (localStorageVars) {
		let utmSource = localStorageVars.model.utm_source;

		if( utmSource === undefined || utmSource === 'undefined' ){
			return false;
		}
		return utmSource;
	}

	return false;
}

function woocommerceUtmMaybeGetVariableFromLocalStorage( variableName ){


	if( undefined === window.localStorage.getItem(variableName) ){
		return false;
	}

	return window.localStorage.getItem(variableName);

	let localStorageVars = woocommerceUtmGetLocalStorageVars();
	if (localStorageVars) {
		let variableValue = localStorageVars.model[variableName];

		if( variableValue === undefined || variableValue === 'undefined' ){
			return false;
		}

		return variableValue;
	}

	return false;
}


function passVariablesToOrder( variables ){

	jQuery.ajax( {
		url: utm_data.ajaxurl,
		method: 'POST',
		dataType: 'html',
		data: {
			'action': 'add_utms_to_order',
			'order_id': utm_data.order_id,
			'variables': variables,
			'nonce' : utm_data.nonce

		}
	} ).done( function( response ) {
		console.log("Sent UTM");
	} );

}


function getVariablesArray(){


	variableToReturn = {};


	// console.log( "utm_data.variableKeys");
	// console.log(utm_data.variableKeys);

	variableKeysObject = JSON.parse( utm_data.variableKeys );

	Object.keys(variableKeysObject).forEach(function(key) {
		// console.log(key);

		keyExists = woocommerceUtmMaybeGetVariableFromLocalStorage(variableKeysObject[key]);
		if ( keyExists ) {
			variableToReturn[variableKeysObject[key]] = keyExists;
		}


	});

	return variableToReturn;

}