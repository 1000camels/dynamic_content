/**
 * Loads content dynamically
 */

(function($) {
$(document).on( 'click', '.dc-item', function( event ) {
        event.preventDefault();
	
	var dc_item = $(this).text();
	var offset = $(this).offset();	
        
	$.ajax({
                url: dcvariables.ajaxurl,
                type: 'post',
                data: {
                        action: 'dc_get_content',
			item: dc_item
                },
                success: function( result ) {
                        //console.log( result );
			var dcObj = jQuery.parseJSON( result );
			if( !$('#dc-box').length ) {
				$('<div/>', {
    					id: 'dc-box'
				}).appendTo($('body'));
			}
			
			$('#dc-box').html( dcObj.post_content ).show();
			$('#dc-box').css({
				'position' : 'absolute',
				'top' : offset.top+'px',
				'right' : '20px'
			});
                }
        })
});
})(jQuery);
