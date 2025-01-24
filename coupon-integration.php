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
add_action( 'woocommerce_before_cart', 'coupon_integration_add_subscription_field' );
add_action( 'woocommerce_before_checkout_form', 'coupon_integration_add_subscription_field' );

function coupon_integration_add_subscription_field() {
    ?>
    <form method="POST" id="subscription-form" class="subscription-form">
        <div class="coupon">
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

// Enqueue JavaScript to handle the AJAX request
add_action( 'wp_enqueue_scripts', function() {
    if ( is_cart() || is_checkout() ) {
       

        // Pass REST API URL to the script
        // wp_localize_script( 'coupon-integration-script', 'couponIntegration', [
        //     'api_url' => rest_url( 'coupon-integration/v1/validate-subscription' ),
        //     'nonce'   => wp_create_nonce( 'wp_rest' ),
        // ] );

        //wp_enqueue_script( 'ajax-script', get_template_directory_uri() . '/js/my-ajax-script.js', array('jquery') );
       

    }

    wp_enqueue_script( 'coupon-integration-script', plugins_url( '/assets/js/coupon-integration.js', __FILE__ ), [ 'jquery' ], '1.0', true );


    wp_localize_script('coupon-integration-script', 'couponIntegration', array(
        'ajax_url' => admin_url('admin-ajax.php')
        ));
} );

// Hook to handle AJAX request to validate subscription ID and apply coupon
add_action( 'wp_ajax_coupon_integration_process_subscription', 'coupon_integration_process_subscription' );
add_action( 'wp_ajax_nopriv_coupon_integration_process_subscription', 'coupon_integration_process_subscription' );

function coupon_integration_process_subscription() {
   

    // if ( ! isset( $_POST['subscription_id'] ) ) {
    //     wp_send_json_error( [ 'message' => 'Subscription ID is missing.' ] );
    // }

    $subscription_id = sanitize_text_field( $_POST['subscription_id'] );
    wp_send_json_success( [ 'message' => $subscription_id ] );

    // // Perform API communication with the remote WordPress site to validate the subscription ID
    // $response = wp_remote_post( 'https://martiniracinggarage.com.au/api/validate-subscription', [
    //     'method'    => 'POST',
    //     'body'      => json_encode( [ 'subscription_id' => $subscription_id ] ),
    //     'headers'   => [
    //         'Content-Type' => 'application/json',
    //     ],
    // ] );

    // // Check for API response errors
    // if ( is_wp_error( $response ) ) {
    //     wp_send_json_error( [ 'message' => $response->get_error_message() ] );
    // }

    // // Decode the API response
    // $body = wp_remote_retrieve_body( $response );
    // $data = json_decode( $body, true );

    // if ( isset( $data['valid'] ) && $data['valid'] === true ) {
    //     wp_send_json_success( [ 'message' => 'Coupon applied successfully!' ] );
    // } else {
    //     wp_send_json_error( [ 'message' => 'Invalid Subscription ID.' ] );
    // }
}

// Register the REST API route
// add_action('rest_api_init', function() {
//     $namespace = 'coupon-integration/v1';
//     $route     = 'validate-subscription';
//     register_rest_route( $namespace, $route, [
//         'methods'             => 'POST',
//         'callback'            => 'coupon_integration_validate',
//     ]);
// });

// function coupon_integration_validate(WP_REST_Request $request) {
//     $subscription_id = $request->get_param( 'subscription_id' );

//     if ( empty( $subscription_id ) ) {
//         return new WP_REST_Response( [
//             'success' => false,
//             'message' => __( 'Subscription ID is missing.', 'coupon-integration' ),
//         ], 400 );
//     }

//     if ( $subscription_id === '123456' ) {
//         return new WP_REST_Response( [
//             'success' => true,
//             'message' => __( 'Subscription ID is valid.', 'coupon-integration' ),
//         ], 200 );
//     }

//     return new WP_REST_Response( [
//         'success' => false,
//         'message' => __( 'Invalid Subscription ID.', 'coupon-integration' ),
//     ], 404 );
// }
