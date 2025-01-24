<?php
/**
 * Plugin Name: Coupon Integration
 * Description: Handles coupon codes, subscriber ID validation, and API communication between two WordPress sites.
 * Version: 1.0
 * Author: Gulam Faruk
 * Text Domain: coupon-integration
 * Domain Path: /languages
 */

// Add input field to WooCommerce cart and checkout pages
add_action( 'woocommerce_before_cart', 'integration_subscription_id' );
add_action( 'woocommerce_before_checkout_form', 'integration_subscription_id' );

function integration_subscription_id() {
    ?>
    <form method="POST" id="subscription-form" class="subscription-form">
        <div class="integration_subscription">
            <input type="text" name="subscription_id" id="subscription_id" class="input-text" placeholder="<?php esc_attr_e( 'Subscription ID', 'coupon-integration' ); ?>">
            <button type="submit" id="apply_subscription" class="button"><?php esc_html_e( 'Apply', 'coupon-integration' ); ?></button>
        </div>
        <div id="error_message" style="margin-top: 10px;"></div>
        <!-- Spinner Icon -->
        <div id="loading_spinner" style="display: none; margin-top: 10px;">
            <img src="<?php echo plugins_url( '/assets/loading.gif', __FILE__ ); ?>" alt="Loading..." />
        </div>
    </form>

    <!-- <img src="https://media.giphy.com/media/3oEjI6SIIHBdRxXI40/giphy.gif"> -->



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
add_action( 'wp_ajax_integration_submit_subscription', 'integration_submit_subscription' );
add_action( 'wp_ajax_nopriv_integration_submit_subscription', 'integration_submit_subscription' );

function integration_submit_subscription() {

    $subscription_id = sanitize_text_field( $_POST['subscription_id'] );
    wp_send_json_success( [ 'message' => $subscription_id ] );

}


