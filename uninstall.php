<?php

/**
 * Exit if not invoked by WordPress.
 */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

function ipay_ghana_uninstall_options() {
    delete_option( 'merchant-key' );
    delete_option( 'success-url' );
    delete_option( 'cancelled-url' );
    delete_option( 'deferred-url' );
    delete_option( 'invoice-id-prefix' );
    delete_option( 'invoice-id-format' );
    delete_option( 'advance-invoice-id-generator' );
}
ipay_ghana_uninstall_options();