<?php
/*
Plugin Name: iPay Ghana
Plugin URI: https://www.ipaygh.com/
Description: Receive payments online in Ghana. Already have an account? Open one with us <a href="https://manage.ipaygh.com/xmanage/get-started">here</a>. Visit your <a href="https://manage.ipaygh.com/xmanage/">dashboard</a> to monitor your transactions.
Version: 0.1.0-alpha
Author: Digital Dreams Ltd.
Author URI: http://www.dareworks.com/
Text Domain:
Domain Path:
License: GNU General Public License v3.0
*/


/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$GLOBALS['default-invoice-id-sequence'] = date( 'ymdHis', time() );
$GLOBALS['custom-invoice-id-sequence']  = $GLOBALS['default-invoice-id-sequence'];

function ipay_ghana_text_domain() {
	load_plugin_textdomain( '', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'ipay_ghana_text_domain' );

function ipay_ghana_admin_styles_and_scripts() {
	wp_enqueue_style( 'ipay-ghana-admin-style', plugins_url( '/assets/css/ipay-ghana-admin.css', __FILE__ ), false, '', 'all' );
	wp_enqueue_script( 'ipay-ghana-admin-script', plugins_url( '/assets/js/ipay-ghana-admin.js', __FILE__ ), false, '' );
}
add_action( 'admin_enqueue_scripts', 'ipay_ghana_admin_styles_and_scripts' );

function ipay_ghana_styles_and_scripts() {
	wp_enqueue_style( 'ipay-ghana-style', plugins_url( '/assets/css/ipay-ghana.css', __FILE__ ), array(), '', 'all' );
	wp_enqueue_script( 'bootstrap', plugins_url('/assets/js/bootstrap.min.js', __FILE__), array( 'jquery' ), '3.3.7', true );
	wp_enqueue_script( 'ipay-ghana-script', plugins_url( '/assets/js/ipay-ghana.js', __FILE__ ), array( 'jquery' ), '', true );
}
add_action( 'wp_enqueue_scripts', 'ipay_ghana_styles_and_scripts' );

function ipay_ghana_plugin_action( $actions, $plugin_file ) {
	if ( false === strpos( $plugin_file, basename( __FILE__ ) ) ) {
		return $actions;
	}
	$settings_link = '<a href="options-general.php?page=ipay-ghana-settings">Settings</a>';

	if ( class_exists( 'WC_Payment_Gateway' ) ) {
		$settings_link .= ' | <a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=ipay-ghana-wc-payment' ) . '">WooCommerce Settings</a>';
	}
	
	array_unshift( $actions, $settings_link );
	return $actions;
}
add_filter( 'plugin_action_links', 'ipay_ghana_plugin_action', 10, 2 );

function ipay_ghana_plugin_support( $meta, $plugin_file ) {
	if ( false === strpos( $plugin_file, basename( __FILE__ ) ) ) {
		return $meta;
	}
	$meta[] = '<a href="https://www.ipaygh.com/pages/help-support" target="_blank">Support </a>';
	return $meta;
}
add_filter( 'plugin_row_meta', 'ipay_ghana_plugin_support', 10, 4 );

function ipay_ghana_admin_settings_options() {
	register_setting( 'ipay-ghana-settings-options-group', 'success-url' );
	register_setting( 'ipay-ghana-settings-options-group', 'deferred-url' );
	register_setting( 'ipay-ghana-settings-options-group', 'merchant-key' );
	register_setting( 'ipay-ghana-settings-options-group', 'cancelled-url' );
    register_setting( 'ipay-ghana-settings-options-group', 'brand-logo-url' );
	register_setting( 'ipay-ghana-settings-options-group', 'invoice-id-format' );
	register_setting( 'ipay-ghana-settings-options-group', 'invoice-id-prefix' );
    register_setting( 'ipay-ghana-settings-options-group', 'advance-invoice-id-generator' );
}

function ipay_ghana_settings_page_help_tab () {
    $screen = get_current_screen();

    $screen->add_help_tab( array(
        'id'	    => 'ipay-ghana-get-started',
        'title'	    => 'Get started',
        'content'	=> '<p>Open an account with iPay Ghana to receive payments online in Ghana by clicking <a href="https://manage.ipaygh.com/xmanage/get-started" target="_blank">here</a>.</p>',
    ) );

	$screen->add_help_tab( array(
		'id'	    => 'ipay-ghana-help-and-support',
		'title'	    => 'Help &amp; Support',
		'content'	=> '<p>Log onto your <a href="https://manage.ipaygh.com/xmanage/" target="_blank">dashboard</a> so as to check your transaction details.</p>
<p>For help and support, please click <a href="https://www.ipaygh.com/pages/help-support" target="_blank">here</a>.</p>',
	) );
}

function ipay_ghana_settings_page() { ?>

    <div class="wrap ipay-ghana">
        <h1>iPay Ghana Settings</h1>
        <p>Define your Merchant key and Invoice ID format here.</p>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'ipay-ghana-settings-options-group' );
            do_settings_sections( 'ipay-ghana-settings-options-group' );
            ?>

            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label>Merchant key</label>
                    </th>
                    <td>
                        <input type="text" title="Enter your Merchant key here" class="regular-text" name="merchant-key" value="<?php echo esc_attr( get_option( 'merchant-key' ) ); ?>"/>
                        <p class="description">Don&rsquo;t have a Merchant key? Register <a href="https://manage.ipaygh.com/xmanage/get-started" target="_blank">here</a> to get started.</p>
                    </td>
                </tr>
	            <tr>
		            <th scope="row">Invoice ID format</th>
		            <td>
			            <fieldset>
				            <legend class="screen-reader-text">
					            <span>Invoice ID Format</span>
				            </legend>
				            <label>
					            <input type="radio" name="invoice-id-format" value="default" checked>
					            <span class="invoice-id-format-text">Default (ymdHsi)</span>
					            <code><?php echo esc_attr( $GLOBALS['default-invoice-id-sequence'] ); ?></code>
				            </label>
				            <br>
				            <label>
					            <input type="radio" name="invoice-id-format" value="custom" <?php checked( 'custom', get_option( 'invoice-id-format' ) ); ?>>
								<span class="invoice-id-format-text">Custom; default, with a prefix: a maximum of three (3) characters.
									<span class="screen-reader-text"> enter a custom invoice id format in the following field</span>
								</span>
				            </label>
				            <label for="invoice-id-prefix" class="screen-reader-text">Custom invoice id format:</label>
				            <input type="text" name="invoice-id-prefix" id="invoice-id-prefix" value="<?php echo esc_attr( get_option( 'invoice-id-prefix' ) ); ?>" class="small-text" maxlength="3" placeholder="WP-">
				            <span class="screen-reader-text">example: </span>
				            <code><span id="example"><?php echo get_option( 'invoice-id-prefix' ) ? : 'WP-';?></span><?php echo esc_attr( $GLOBALS['default-invoice-id-sequence'] ); ?></code>
				            <br>
				            <label>
					            <input type="radio" name="invoice-id-format" value="advance" <?php checked( 'advance', get_option( 'invoice-id-format' ) ); ?>>
					            <span class="invoice-id-format-text">Advance, for full control (if you know what you are doing)</span>
				            </label>
				            <br>
				            <div id="advance-invoice-id-generator-block" style="display: none;">
					            <p>Before using the Advance option so as to define your fully customised Invoice ID format, be mindful that:</p>
					            <ol>
						            <li>Your function should only return an instance (ideally, an incremental sequence, with respect to your desired invoice ID format), when accessed.</li>
						            <li>The generated invoice ID instance ought to be unique per transaction in order for the later to be successful.</li>
						            <li>The generated invoice ID instance should not exceed the maximum allowed characters count for such a purpose; either: fifteen (15) characters.</li>
					            </ol>
					            <p>
						            <label for="advance-invoice-id-generator">Put your custom invoice ID instance generator function in the box provided below:</label>
					            </p>
					            <p>
						            <textarea name="advance-invoice-id-generator" rows="10" cols="50" id="advance-invoice-id-generator" class="large-text code" readonly><?php echo "Will be enabled in our next update.\r\nCurrently disabled and will return the default Invoice ID format if selected."; ?></textarea>
					            </p>
				            </div>
			            </fieldset>
		            </td>
	            </tr>
                <tr>
                    <th scope="row">
                        <label>Brand logo URL</label>
                    </th>
                    <td>
                        <input type="text" title="Define a URL to load your brand logo" class="regular-text" name="brand-logo-url" value="<?php echo esc_attr( get_option( 'brand-logo-url' ) ); ?>"/>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label>Success URL</label>
                    </th>
                    <td>
                        <input type="text" title="Define a URL to redirect on success here" class="regular-text" name="success-url" value="<?php echo esc_attr( get_option( 'success-url' ) ); ?>"/>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label>Cancelled URL</label>
                    </th>
                    <td>
                        <input type="text" title="Define a URL to redirect on cancel here" class="regular-text" name="cancelled-url" value="<?php echo esc_attr( get_option( 'cancelled-url' ) ); ?>"/>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label>Deferred URL</label>
                    </th>
                    <td>
                        <input type="text" title="Define a URL to redirect on defer here" class="regular-text" name="deferred-url" value="<?php echo esc_attr( get_option( 'deferred-url' ) ); ?>"/>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>

        </form>
    </div>
<?php }

function ipay_ghana_settings_init() {
    if ( current_user_can( 'manage_options') ) {
        $ipay_ghana_settings_page = add_options_page( 'iPay Ghana Settings', 'iPay Ghana', 'administrator', 'ipay-ghana-settings', 'ipay_ghana_settings_page' );

        add_action( 'load-' . $ipay_ghana_settings_page, 'ipay_ghana_settings_page_help_tab' );

        add_action( 'admin_init', 'ipay_ghana_admin_settings_options' );
    }
}
add_action( 'admin_menu', 'ipay_ghana_settings_init' );

class Ipay_Ghana_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'ipay-ghana-widget',
			esc_html__( 'iPay Ghana', '' ),
			array( 'description' => esc_html__( 'Receive payments online in Ghana.', '' ) )
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		if ( ! empty( $instance['payment-collection-title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['payment-collection-title'] ) . $args['after_title'];
		}

		if ( ! empty( $instance['payment-collection-btn-name'] ) ) {
			echo '<button type="button" class="btn btn-info" data-toggle="modal" data-backdrop="static" data-target="#ipay-ghana-payment-modal">' . apply_filters( 'widget_title', $instance['payment-collection-btn-name'] ) . '</button>';
		} ?>

		<div id="ipay-ghana-payment-modal" class="modal fade" role="dialog" tabindex="-1">
			<div class="modal-dialog">
				<div class="modal-content">
					<form method="post" action="https://community.ipaygh.com/gateway" id="ipay-ghana-payment-form" target="_blank">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<?php echo ! empty( get_option( 'brand-logo-url' )  !== '' ) ?
								'<img src="' .get_option( 'brand-logo-url' ) . '" width="180px" height="70px" class="center-block" alt="' . get_bloginfo( 'name' ) . '"/>' :
								'<h4 class="lead">Payment Details</h4>';
							?>

						</div>
						<div class="modal-body">
							<?php echo ! empty( get_option( 'brand-logo-url' )  !== '' ) ? '<h4 class="lead">Payment Details</h4>' : ''; ?>

							<input type="hidden" name="merchant_key" value="<?php echo get_option( 'merchant-key' ); ?>">
							<input type="hidden" name="success_url" value="<?php echo get_option( 'success-url' ); ?>">
							<input type="hidden" name="cancelled_url" value="<?php echo get_option( 'cancelled-url' ); ?>">
							<input type="hidden" name="deferred_url" value="<?php echo get_option( 'deferred-url' ); ?>"><?php

							switch ( get_option( 'invoice-id-format' ) ) {
								case 'custom':
									echo '<input type="hidden" name="invoice_id" value="' . get_option( 'invoice-id-prefix' ) . $GLOBALS['default-invoice-id-sequence'] .'">';
									break;
								case 'advance':
									echo '<!-- advance_invoice-id_sequence (Currently disabled and set to the default Invoice ID format; will be enabled in our next update.) -->';
									echo '<input type="hidden" name="invoice_id" value="' . $GLOBALS['custom-invoice-id-sequence'] . '">';
									break;
								default:
									echo '<input type="hidden" name="invoice_id" value="' . $GLOBALS['default-invoice-id-sequence'] . '">';
									break;
							} ?>

							<div class="row">
								<div class="form-group col-xs-12 col-sm-6">
									<label for="extra-name">Name</label>
									<input type="text" id="extra-name" name="extra_name" required>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-xs-12 col-sm-3">
									<label for="total">Payment Amount</label>
									<input type="text" id="total" name="total" required>
								</div>
								<div class="form-group col-xs-12 col-sm-3">
									<label for="extra-mobile">Contact Number</label>
									<input type="text" id="extra-mobile" name="extra_mobile" required>
								</div>
								<div class="form-group col-xs-12 col-sm-6">
									<label for="extra-email">Email [Optional]</label>
									<input type="text" id="extra-email" name="extra_email">
								</div>
							</div>
							<div class="row">
								<div class="form-group col-xs-12">
									<label for="description">Description of Payment/ Item Order Number</label>
									<textarea rows="3" id="description" name="description" required><?php echo apply_filters( 'widget_title', $instance['payment-collection-description'] );?></textarea>
								</div>
							</div>
							<div id="ipay-ghana-payment-progress" style="display: none;"></div>
							<div id="ipay-ghana-payment-summary" class="well well-sm" style="display: none;"></div>
						</div>
						<div class="modal-footer">
							<img class="powered-by" src="<?php echo plugins_url( '/assets/img/powered-by-ipay-ghana.jpeg', __FILE__ );?>" alt="Powered by iPay Ghana" />
							<button type="button" class="btn btn-default" id="ipay-ghana-dismiss-modal" style="display: none;" data-dismiss="modal"></button>
							<button class="btn btn-cancel" id="ipay-ghana-pay" style="display: none;"></button>
							<button class="btn btn-cancel" id="ipay-ghana-check-status" style="display: none;"></button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<?php echo $args['after_widget'];
	}

	public function form( $instance ) {
		$title = ! empty( $instance['payment-collection-title'] ) ? $instance['payment-collection-title'] : esc_html__( 'iPay Ghana Payment', '' );
		$payment_collection_btn_name = ! empty( $instance['payment-collection-btn-name'] ) ? $instance['payment-collection-btn-name'] : esc_html__( 'Pay', '' );
		$payment_collection_desc = ! empty( $instance['payment-collection-description'] ) ? $instance['payment-collection-description'] : esc_html__( '', '' );
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'payment-collection-title' ) ); ?>"><?php esc_attr_e( 'Title:', '' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'payment-collection-title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'payment-collection-title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'payment-collection-btn-name' ) ); ?>"><?php esc_attr_e( 'Label of button:', '' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'payment-collection-btn-name' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'payment-collection-btn-name' ) ); ?>" type="text" value="<?php echo esc_attr( $payment_collection_btn_name ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'payment-collection-description' ) ); ?>"><?php esc_attr_e( 'Description of collection method:', '' ); ?></label>
			<textarea class="widefat" rows="3" id="<?php echo esc_attr( $this->get_field_id( 'payment-collection-description' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'payment-collection-description' ) ); ?>"><?php echo esc_attr( $payment_collection_desc ); ?></textarea>
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = [];
		$instance['payment-collection-title'] = ( ! empty( $new_instance['payment-collection-title'] ) ) ? strip_tags( $new_instance['payment-collection-title'] ) : '';
		$instance['payment-collection-btn-name'] = ( ! empty( $new_instance['payment-collection-btn-name'] ) ) ? strip_tags( $new_instance['payment-collection-btn-name'] ) : '';
		$instance['payment-collection-description'] = ( ! empty( $new_instance['payment-collection-description'] ) ) ? strip_tags( $new_instance['payment-collection-description'] ) : '';
		return $instance;
	}

}

function register_ipay_ghana_widget() {
	register_widget( 'Ipay_Ghana_Widget' );
}
add_action( 'widgets_init', 'register_ipay_ghana_widget' );

function init_ipay_ghana_wc_payment_gateway() {
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		exit;
	}

	class Ipay_Ghana_WC_Payment_Gateway extends WC_Payment_Gateway {
		public function __construct() {
			$this->id                   = 'ipay-ghana-wc-payment';
			$this->icon                 = plugins_url( '/assets/img/powered-by-ipay-ghana.jpeg', __FILE__ );
			$this->has_fields           = true;
			$this->method_title         = __( 'iPay Ghana Payment', '' );
			$this->method_description   = __( 'Receive mobile payments on your WooCommerce store in Ghana.', '' );
			$this->init_form_fields();
			$this->init_settings();
			$this->title                = $this->get_option( 'title' );
			$this->description          = $this->get_option( 'description' );

			add_action( 'admin_notices', array( $this, 'do_ssl_check' ) );
		}

		public function do_ssl_check() {
			if ( $this->enabled === 'yes' ) {
				if ( get_option( 'woocommerce_force_ssl_checkout' ) === 'no' ) {
					echo '<div class="error"><p>' . sprintf( __( '<strong>%s</strong> is enabled and WooCommerce is not forcing the SSL certificate on your checkout page. Please ensure that you have a valid SSL certificate and that you are <a href="%s">forcing the checkout pages to be secured</a>.' ), $this->method_title, admin_url( 'admin.php?page=wc-settings&tab=checkout' ) ) . '</p></div>';
				}
			}
		}

		public function init_form_fields() {
			$this->form_fields = array(
				'enabled' => array(
					'title'       => __( 'Enable/Disable', '' ),
					'type'        => 'checkbox',
					'label'       => __( 'Enable iPay Ghana Payment', '' ),
					'default'     => 'no'
				),
				'title' => array(
					'title'       => __( 'Title', '' ),
					'type'        => 'text',
					'description' => __( 'This controls the title which the user sees during checkout.', '' ),
					'default'     => __( 'iPay Ghana Payment', '' ),
					'desc_tip'    => true,
				),
				'description' => array(
					'title'       => __( 'Description', '' ),
					'type'        => 'textarea',
					'description' => __( 'Payment method description that the customer will see on your checkout.', '' ),
					'default'     => __( 'You will be redirected to the iPay Ghana Check Out page so as to proceed with the payment.', '' ),
					'desc_tip'    => true,
				),
			);
		}

		public function payment_fields() { ?>

			<p class="form-group">
				<label for="network_operator">Select Network</label>
				<select id="network_operator" class="" name="extra_wallet_issuer_hint" required>
					<option disabled selected value> -- Select One -- </option>
					<option value="airtel">Airtel Money</option>
					<option value="mtn">MTN Mobile Money</option>
					<option value="tigo">tiGO Cash</option>
				</select>
			</p>
			<p class="form-row form-row validate-required validate-phone" id="wallet_number_field">
				<label for="mobile_wallet_number">Phone Number <abbr class="required" title="required">*</abbr></label>
				<input type="tel" class="input-text" id="mobile_wallet_number" name="pymt_instrument" placeholder="Enter your wallet number here." autocomplete="on" required>
			</p>
		<?php }
		
		public function process_payment( $order_id ) {
			$order = wc_get_order( $order_id );

			$api = 'https://community.ipaygh.com/v1/';

			$payload = [
				'merchant_key'               => get_option( 'merchant-key' ),
				'success_url'                => get_option( 'success-url' ),
				'cancelled_url'              => get_option( 'cancelled-url' ),
				'deferred_url'               => get_option( 'deferred-url' ),
				'total'             	     => $order->order_total,
				'invoice_id'                 => str_replace( '#', '', $order->get_order_number() ),
				'extra_wallet_issuer_hint'   => ( isset( $_POST['extra_wallet_issuer_hint'] ) && ! empty( $_POST['extra_wallet_issuer_hint'] ) ) ? $_POST['extra_wallet_issuer_hint'] : $_POST['extra_wallet_issuer_hint'],
				'pymt_instrument'            => $wallet_number = ( isset( $_POST['pymt_instrument'] ) && ! empty( $_POST['pymt_instrument'] ) ) ? $_POST['pymt_instrument'] : $_POST['pymt_instrument'],
				'extra_name'         	     => $order->billing_first_name . ' ' . $order->billing_last_name,
				'extra_mobile'         	     => $order->billing_phone,
				'extra_email'          	     => $order->billing_email,
				'description'          	     => get_bloginfo( 'name' ) . ' WooCommerce transaction Order ID: ' . $order_id,
			];

			$response = wp_remote_post( $api . 'mobile_agents_v2', [
				'method'    => 'POST',
				'body'      => http_build_query( $payload ),
				'timeout'   => 90,
				'sslverify' => false,
			] );

			if ( is_wp_error( $response ) ) {
				throw new Exception( __( 'We are currently experiencing problems trying to connect to this payment gateway. Sorry for the inconvenience.', '' ) );
			}

			if ( $response['response']['code'] === 500 ) {
				throw new Exception( __( 'An error was encountered. Please contact us with error code (' . $response['response']['code'] . ').', '' ) );
			}

			$response_body = wp_remote_retrieve_body( $response );
			$data = json_decode( $response_body, true );

			if ( $response['response']['code'] === 200 ) {
				if ( ( $data['success'] === true ) && ( $data['status'] === 'new' ) ) {
					$order->add_order_note( __( 'Transaction initiated successfully; a USSD prompt or message with Mobile Money payment completion steps has been triggered and sent to: ' . $wallet_number . '.', '' ) );
					$order->update_status( 'on-hold', __( 'Awaiting Mobile Money payment.<br>', '' ) );
					$order->reduce_order_stock();
					WC()->cart->empty_cart();

					return [
						'result'   => 'success',
						'redirect' => $this->get_return_url( $order ),
					];
				} else {
					wc_add_notice( __('Payment error:', '') . $response['response']['message'], 'error' );
					return null;
				}
			} else {
				wc_add_notice( 'An error was encountered. Please contact us with error code (' . $response['response']['code'] . ').', 'error' );
				$order->add_order_note( 'Error code: ' . $response['response']['code'] . PHP_EOL . 'Status: ' . $response['response']['status'] );
			}
			return null;
		}
	}

	function ipay_ghana_wc_payment_gateway_label( $methods ) {
		$methods[] = 'Ipay_Ghana_WC_Payment_Gateway';
		return $methods;
	}
	add_filter( 'woocommerce_payment_gateways', 'ipay_ghana_wc_payment_gateway_label' );
}
add_action( 'plugins_loaded', 'init_ipay_ghana_wc_payment_gateway', 0 );
