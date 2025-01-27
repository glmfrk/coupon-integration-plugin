<?php
/**
 * Plugin Name: Coupon Integration
 * Description: Handles coupon codes, subscriber ID validation, and API communication between two WordPress sites.
 * Version: 1.0
 * Author: Wooxperto
 * Text Domain: coupon-integration
 * Domain Path: /languages
 */

// Add input field to WooCommerce cart and checkout pages
add_action( 'woocommerce_before_cart', 'integration_subscription_id' );
add_action( 'woocommerce_before_checkout_form', 'integration_subscription_id' );

function integration_subscription_id() {
    ?>
    <form method="POST" id="subscription-form" class="subscription-form">
        <div class="form_item" style="display:flex; align-item:center; gap:10px;">
            <div class="integration_subscription" style="display: flex;gap: 5px;">
                <input type="text" name="subscription_id" id="subscription_id" class="input-text" placeholder="<?php esc_attr_e( 'Subscription ID', 'coupon-integration' ); ?>">
                <button type="submit" id="apply_subscription" class="button"><?php esc_html_e( 'Apply', 'coupon-integration' ); ?></button>
            </div>
            <!-- Spinner Icon -->
            <div id="loading_spinner" style="display: none; margin-top: 10px; width:24px; height:24px;">
                <img src="<?php echo plugins_url( '/assets/loading.gif', __FILE__ ); ?>" alt="Loading..." />
            </div>
        </div>
        <div id="error_message" style="margin-top: 10px;"></div>
     
    </form>

    <?php
}



add_action( 'wp_enqueue_scripts', 'coupon_integration_enqueue_scripts' );
function coupon_integration_enqueue_scripts() {
    wp_enqueue_script(
        'coupon-integration-script',
        plugins_url( '/assets/js/coupon-integration.js', __FILE__ ),
        array( 'jquery' ),
        '1.0',
        true
    );

    wp_localize_script( 'coupon-integration-script', 'couponIntegration', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
    ));

}


// Add AJAX action for subscription field submission
add_action('wp_ajax_integration_submit_subscription', 'integration_submit_subscription');
add_action('wp_ajax_nopriv_integration_submit_subscription', 'integration_submit_subscription');

function integration_submit_subscription() {
    
    if (empty($_POST['subscription_id'])) {
        wp_send_json_error(['message' => 'Subscription ID is required.']);
        exit;
    }

    $subscription_id = sanitize_text_field($_POST['subscription_id']);
    $subscription_id = trim($subscription_id);
    // Prepare data to send to Website B
    $api_url = 'https://martiniracinggarage.com.au/wp-json/custom/api/subscriptionid';
    $response = wp_remote_get($api_url . '?subscription_id=' . urlencode($subscription_id), [
        'timeout' => 45,
        'headers' => [
            'Content-Type' => 'application/json',
        ],
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'Failed to communicate with the API.']);
        exit;
    }

    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);
    $response_data = json_decode($response_body, true);

    if ($response_code === 200) {
        $coupon_code = $response_data['coupon_code'];

        if(WC()->cart->get_cart_contents_count() > 0){

            // check if there is any coupon already applied
            if( count( WC()->cart->get_applied_coupons() ) > 0 ) {
                foreach ( WC()->cart->get_coupons() as $code => $coupon ){
                    WC()->cart->remove_coupon( $code );
                }

            }

            WC()->cart->apply_coupon( $coupon_code );
            wp_send_json_success([
                'message' => 'Discount applied in your cart.',
                'subscription_id' => $response_data['coupon_code'], // Ensure subscription ID is returned
            ]);
        }else{
            wp_send_json_error(['message' => 'Cart is empty!', 'data' => '']);
        }
    } else {
        wp_send_json_error(['message' => $response_data['message'], 'data' => $response_body]);
    }

}


