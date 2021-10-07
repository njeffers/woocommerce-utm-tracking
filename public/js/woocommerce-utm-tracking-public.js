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
