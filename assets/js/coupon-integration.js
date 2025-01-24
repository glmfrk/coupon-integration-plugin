jQuery(function($) {
    jQuery('#subscription-form').on('submit', function(event) {
        event.preventDefault();
        var subscription_id = jQuery('#subscription_id').val();
    
        jQuery('#error_message').html('');
        jQuery('#loading_spinner').show();

        if (subscription_id.trim() === '') {
            jQuery('#error_message').html('<span style="color: red;">Subscription ID is required.</span>');
            jQuery('#loading_spinner').hide();
            return;
        }

        jQuery.ajax({
        type:"POST",
        url:couponIntegration.ajax_url,
        data: {action:'coupon_integration_process_subscription',
            subscription_id: subscription_id,
        },
        success:function(response){
            console.log(response);
            if (response.success) {
                jQuery('#error_message').html('<span style="color: green;">' + response.data.message + '</span>');
            } else {
                jQuery('#error_message').html('<span style="color: red;">' + response.data.message + '</span>');
            }
            jQuery('#loading_spinner').hide();
        },
        error: function() {
            jQuery('#error_message').html('<span style="color: red;">An error occurred, please try again.</span>');
            jQuery('#loading_spinner').hide();
        }
        });

    });
});
