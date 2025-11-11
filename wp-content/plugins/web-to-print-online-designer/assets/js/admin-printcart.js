'use strict';

jQuery(document).ready(function ($) {
	var ajaxUrl = typeof admin_nbds !== 'undefined' ? admin_nbds.url : ajax_url;
	jQuery("#printcart-connection-width-dashboard").on("click", function (e) {
	    var isHttps = jQuery(this).data("is-https");
	    var isConnected = jQuery(this).data("is-connected");
	    if(isConnected) {
	    	window.open(admin_nbds.printcart_dashboard_url , '_blank');
	    	return;
	    }
	    if(!isHttps) return;
	    var url = jQuery(this).data("url");
	    var text = jQuery(this).html();
	    var shopUrl = jQuery(this).data('shop-url');
	    var email = jQuery(this).data('user-email');
	    var storeName = jQuery(this).data('store-name');
	    var userName = jQuery(this).data('user-name');
	    var result = jQuery('.printcart-results');
	    result.html("");
	    jQuery.ajax({
	        type: "post",
	        dataType: "json",
	        url: ajaxUrl,
	        data: {
	            action: "printcart_generate_key_api",
	            shopUrl: shopUrl,
	            email: email,
	            storeName: storeName,
	            userName: userName,
	        },
	        context: this,
	        beforeSend: function () {
	            jQuery(this).html("Generating key...");
	            jQuery(this).attr("disabled", "disabled");
	        },
	        success: function (response) {
	            jQuery(this).attr("disabled", false);
	            jQuery(this).html(text);
	            if (response.success && response.data) {
	                if (response.data.flag && response.data.secret && response.data.sid && response.data.unauth_token) {
	                    jQuery('.nbd-setup-actions button.button-next').prop('disabled', false);
	                    jQuery('#nbdesigner_printcart_api_sid').val(response.data.sid);
	                    jQuery('#nbdesigner_printcart_api_secret').val(response.data.secret);
	                    jQuery('#nbdesigner_printcart_api_unauth_token').val(response.data.unauth_token);
	                    result.html("<b>Connect successfully!</b>");
	                    result.css('color', '#0f631e')
	                } else {
	                    jQuery(this).attr("disabled", false);
	                    result.html(response.data.message);
	                    result.css('color', '#f11')
	                }
	            }
	        },
	        error: function (error) {
	            jQuery(this).attr("disabled", false);
	            jQuery(this).html("Try again");
	        },
	    });
	});
	jQuery('.pc-check-connection-dashboard').on('click', function() {
		var text = jQuery(this).html();
		var sid = jQuery('#nbdesigner_printcart_api_sid').val();
		var secret = jQuery('#nbdesigner_printcart_api_secret').val();
		var unauth_token = jQuery('#nbdesigner_printcart_api_unauth_token').val();
		var result_check = jQuery('.printcart-results-check');
		result_check.html('');

		jQuery.ajax({
	        type: "post",
	        dataType: "json",
	        url: ajaxUrl,
	        data: {
	            action: "printcart_check_connection_dashboard",
	            sid: sid,
	            secret: secret,
	            unauth_token: unauth_token,
	        },
	        context: this,
	        beforeSend: function () {
	            jQuery(this).html("Checking...");
	            jQuery(this).attr("disabled", "disabled");
	        },
	        success: function (response) {
	            jQuery(this).attr("disabled", false);
	            jQuery(this).html(text);
	            if (response.success && response.data) {
	                result_check.html('<div style="color: #0f631e"><b>Connected.</b></div>')
	            } else {
	            	result_check.html('<div style="color: #f11"><b>Error.</b></div>')
	            }
	        },
	        error: function (error) {
	            jQuery(this).attr("disabled", false);
	            jQuery(this).html("Try again");
	        },
	    });
	})
});
