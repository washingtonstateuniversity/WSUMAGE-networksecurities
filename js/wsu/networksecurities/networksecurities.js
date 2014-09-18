
document.observe('dom:loaded', function () {
    //$('url').hide();
});

(function($,undefined){
	var capState,methods;
	
	capState = undefined;
	methods = {
		init : function(options) {
			var settings,capsLockForcedUppercase,helpers;
			
			settings = $.extend({}, options);// No defaults yet
			
			// Some systems will always return uppercase characters if Caps Lock is on. 
			capsLockForcedUppercase = /MacPPC|MacIntel/.test(window.navigator.platform) === true;

			helpers = {
				isCapslockOn : function(e) {
					var shiftOn,keyString;
					
					shiftOn = false;
					
					if (e.shiftKey) { // determines whether or not the shift key was held
						shiftOn = e.shiftKey; // stores shiftOn as true or false
					} else if (e.modifiers) { // determines whether or not shift, alt or ctrl were held
						shiftOn = !!(e.modifiers & 4);
					}
					
					keyString = String.fromCharCode(e.which);
					
					if (keyString.toUpperCase() === keyString.toLowerCase()) {
						// We can't determine the state for these keys
					} else if (keyString.toUpperCase() === keyString) {
						if (capsLockForcedUppercase === true && shiftOn) {
							// We can't determine the state for these keys
						} else {
							capState = !shiftOn;
						}
					} else if (keyString.toLowerCase() === keyString) {
						capState = shiftOn;
					}
					return capState;
				},

				isCapslockKey : function(e) {
					var keyCode;
					keyCode = e.which;
					if (keyCode === 20) {
						if (capState !== undefined) {
							capState = !capState;
						}
					}
					return capState;
				},

				hasStateChange : function(previousState, currentState) {
					if (previousState !== currentState) {
						$('body').trigger("capsChanged");
						if (currentState === true) {
							$('body').trigger("capsOn");
						} else if (currentState === false) {
							$('body').trigger("capsOff");
						} else if (currentState === undefined) {
							$('body').trigger("capsUnknown");
						}
					}
				},

			};
			$('body').on("keypress.capState", function(event) {// Check all keys
				var previousState;
				previousState = capState;
				capState = helpers.isCapslockOn(event);
				helpers.hasStateChange(previousState, capState);
			});
			$('body').on("keydown.capState", function(event) {// Check if key was Caps Lock key
				var previousState;
				previousState = capState;
				capState = helpers.isCapslockKey(event);
				helpers.hasStateChange(previousState, capState);
			});
			$(window).on("focus.capState", function() {// If the window loses focus then we no longer know the state
				var previousState;
				previousState = capState;
				capState = undefined;
				helpers.hasStateChange(previousState, capState);
			});
			helpers.hasStateChange(null, undefined);// Trigger events on initial load of plugin
			return this.each(function() {});// Maintain chainability
		},
		state : function() {
			return capState;
		},
		destroy : function() {
			return this.each(function() {
				$('body').off('.capState');
				$(window).off('.capState');
			})
		}
	}
	jQuery.fn.capState = function(method) {
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Method ' + method + ' does not exist on jQuery.capState');
		}
	};
})(jQuery,undefined);





(function($){
	$(function(){
		
		var stateId,stateMessage;
		if( $("input[type='password']").length ){
			$(window).capState();
			stateId = "capState";
			stateMessage = '<div id="'+stateId+'">Caps lock is on</div>';
			
			$("input[type='password']").wrap('<div id="capMessages">');

			$(window).on("capsOn", function(event) {
				$('#capMessages').append(stateMessage);
			});
			$(window).on("capsOff capsUnknown", function(event) {
				$('#capMessages #'+stateId+'').remove();
			});
			$("input").on("change", function(event) {
				if ($(window).capState("state") === true) {
					if($('#capMessages #'+stateId+'').length<=0){
						$('#capMessages').append(stateMessage);
					}
				}else{
					$('#capMessages #'+stateId+'').remove();
				}
			});
		}
		
		$('.sso_login').on('click',function(e){
			e.preventDefault();
			e.stopPropagation();

			var  screenX    = typeof window.screenX != 'undefined' ? window.screenX : window.screenLeft;
			var	 screenY    = typeof window.screenY != 'undefined' ? window.screenY : window.screenTop;
			var	 outerWidth = typeof window.outerWidth != 'undefined' ? window.outerWidth : document.body.clientWidth;
			var	 outerHeight = typeof window.outerHeight != 'undefined' ? window.outerHeight : (document.body.clientHeight - 22);
			var	 width    = $(this).data("width");
			var	 height   = $(this).data("height");
			var	 left     = parseInt(screenX + ((outerWidth - width) / 2), 10);
			var	 top      = parseInt(screenY + ((outerHeight - height) / 2.5), 10);
			var	 features = (
					'width=' + width +
					',height=' + height +
					',left=' + left +
					',top=' + top
				  );
			var url = $(this).data("url");
			var newwindow=window.open(url,'Login_by_facebook',features);
			if (window.focus) {
				newwindow.focus()
			}
		});
	});
})(jQuery);



