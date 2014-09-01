
document.observe('dom:loaded', function () {
    //$('url').hide();
});

(function($){
	$(function(){
		$('.sso_login').on('click',function(e){
			e.preventDefault();
			e.stopPropagation();
			
			var url = $(this).data("url");
			
			var newwindow;
			var intId;
			var  screenX    = typeof window.screenX != 'undefined' ? window.screenX : window.screenLeft;
			var	 screenY    = typeof window.screenY != 'undefined' ? window.screenY : window.screenTop;
			var	 outerWidth = typeof window.outerWidth != 'undefined' ? window.outerWidth : document.body.clientWidth;
			var	 outerHeight = typeof window.outerHeight != 'undefined' ? window.outerHeight : (document.body.clientHeight - 22);
			var	 width    = 500;
			var	 height   = 270;
			var	 left     = parseInt(screenX + ((outerWidth - width) / 2), 10);
			var	 top      = parseInt(screenY + ((outerHeight - height) / 2.5), 10);
			var	 features = (
					'width=' + width +
					',height=' + height +
					',left=' + left +
					',top=' + top
				  );
		
			newwindow=window.open(url,'Login_by_facebook',features);
			if (window.focus) {
				newwindow.focus()
			}
			return false;
		});
	});
})(jQuery);



