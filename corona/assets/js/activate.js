/* PressMart theme activation*/
jQuery( function ( $ ) {
	"use strict";
	$('body').on('click', '.corona-activate-btn', function() {
		alert('tesst');
		var purchase_code = $(".purchase-code").val();
		var activate_btn = $(this);
		activate_btn.addClass('loading');
		if( $.trim(purchase_code) != ''){
		$(this).attr('disabled', 'true');
		$.ajax({
			url: ajaxurl,
			method: 'POST',
			dataType: 'json',
			data: {
				purchase_code : purchase_code,
				action : 'active_theme',
			}
		})
		.done(function(response){
	
		});
	} else {
	alert('Please Enter Purchase Code');
	}
	
	return false;
	});
	
	});