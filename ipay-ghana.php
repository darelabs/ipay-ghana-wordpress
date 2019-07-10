<?php
/*
Plugin Name: iPay Ghana
Plugin URI: https://www.ipaygh.com/
Description: Receive payments on your WordPress website in Ghana. Already have an account? Open one with us <a href="https://manage.ipaygh.com/xmanage/get-started">here</a>. Visit your <a href="https://manage.ipaygh.com/xmanage/">dashboard</a> to monitor your transactions.
Version: 1.0.3
Author: iPay Solutions Ltd.
Author URI: https://www.ipaygh.com/
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
	//register_setting( 'ipay-ghana-settings-options-group', 'deferred-url' );
	register_setting( 'ipay-ghana-settings-options-group', 'merchant-key' );
	register_setting( 'ipay-ghana-settings-options-group', 'cancelled-url' );
    register_setting( 'ipay-ghana-settings-options-group', 'ipay-ghana-currency' );
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
        <p>Define your Merchant key, invoice ID here.</p>
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
						            <textarea name="advance-invoice-id-generator" rows="10" cols="50" id="advance-invoice-id-generator" class="large-text code" readonly><?php echo "May be enabled in our future updates.\r\nCurrently disabled and will return the default Invoice ID format when selected."; ?></textarea>
					            </p>
				            </div>
			            </fieldset>
		            </td>
	            </tr>
				<tr>
                    <th scope="row">
                        <label for="ipay-ghana-currency">Currency</label>
                    </th>
                    <td>
                        <select title="Define a currency" name="ipay-ghana-currency" id="ipay-ghana-currency">
							<!-- <option <?php echo esc_attr( strtoupper( get_option( 'ipay-ghana-currency' ) ) === "GBP" ) ? 'selected="selected"' : ''; ?> value="GBP">Great Britain Pound (GBP)</option> -->
							<option <?php echo esc_attr( strtoupper( get_option( 'ipay-ghana-currency' ) ) === "GHS" ) ? 'selected="selected"' : ''; ?> value="GHS">Ghana Cedis (GHS)</option>
							<!-- <option <?php echo esc_attr( get_option( strtoupper( 'ipay-ghana-currency' ) ) === "EUR" ) ? 'selected="selected"' : ''; ?> value="EUR">Euro (EUR)</option>
							<option <?php echo esc_attr( get_option( strtoupper( 'ipay-ghana-currency' ) ) === "USD" ) ? 'selected="selected"' : ''; ?> value="USD">United States Dollar (USD)</option> -->
						</select>
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
                        <input type="text" title="The page to which iPay will redirect the user after user completes the iPay checkout process. Please note that this does not mean that payment has been received!" class="regular-text" name="success-url" value="<?php echo esc_attr( get_option( 'success-url' ) ); ?>"/>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label>Cancelled URL</label>
                    </th>
                    <td>
                        <input type="text" title="The page to which iPay will redirect the user after user cancels payment" class="regular-text" name="cancelled-url" value="<?php echo esc_attr( get_option( 'cancelled-url' ) ); ?>"/>
                    </td>
                </tr>
                <!-- <tr>
                    <th scope="row">
                        <label>Deferred URL</label>
                    </th>
                    <td>
                        <input type="text" title="Define a URL to redirect on defer here" class="regular-text" name="deferred-url" value="<?php echo esc_attr( get_option( 'deferred-url' ) ); ?>"/>
                    </td>
                </tr> -->
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
			array( 'description' => esc_html__( 'Receive payments online on your WordPress website in Ghana.', '' ) )
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
					<form method="post" action="https://manage.ipaygh.com/gateway/checkout" id="ipay-ghana-payment-form" target="_blank">
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
							<input type="hidden" name="currency" value="<?php echo get_option( 'ipay-ghana-currency' ); ?>">
							<input type="hidden" name="success_url" value="<?php echo get_option( 'success-url' ); ?>">
							<input type="hidden" name="cancelled_url" value="<?php echo get_option( 'cancelled-url' ); ?>">
							<input type="hidden" name="source" value="WORDPRESS">
							<input type="hidden" id="ipay-ghana-currency" value="<?php echo esc_attr( get_option( "ipay-ghana-currency" ) ); ?>" name="currency">
							<?php
								switch ( get_option( 'invoice-id-format' ) ) {
									case 'custom':
										echo '<input type="hidden" name="invoice_id" value="' . get_option( 'invoice-id-prefix' ) . $GLOBALS['default-invoice-id-sequence'] .'">';
										break;
									case 'advance':
										echo '<!-- advance_invoice-id_sequence (Currently disabled and set to the default Invoice ID format; May be enabled in our future updates.) -->';
										echo '<input type="hidden" name="invoice_id" value="' . $GLOBALS['custom-invoice-id-sequence'] . '">';
										break;
									default:
										echo '<input type="hidden" name="invoice_id" value="' . $GLOBALS['default-invoice-id-sequence'] . '">';
										break;
								} 
							?>
							<div class="row">
								<div class="form-group col-xs-12 col-sm-6">
									<label for="extra-name">Name</label>
									<input type="text" id="extra-name" name="extra_name" required>
								</div>
								<div class="form-group col-xs-12 col-sm-6">
									<label for="extra-mobile">Contact Number</label>
									<input type="text" id="extra-mobile" name="extra_mobile" required>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-xs-12 col-sm-6">
									<label for="extra-email">Email [Optional]</label>
									<input type="text" id="extra-email" name="extra_email">
								</div>
								<div class="form-group col-xs-12 col-sm-6">
									<label for="total">Amount</label>
									<input type="text" id="total" name="total" required>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-xs-12">
									<label for="description">Description of Payment/ Item Order Number</label>
									<textarea rows="2" id="description" name="description" required><?php echo apply_filters( 'widget_title', $instance['payment-collection-description'] );?></textarea>
								</div>
							</div>
							<div id="ipay-ghana-payment-progress" style="display: none;"></div>
							<div id="ipay-ghana-payment-summary" class="well well-sm" style="display: none;"></div>
						</div>
						<div class="modal-footer">
							<img class="powered-by" src="https://payments.ipaygh.com/app/webroot/img/iPay_payments.png" alt="Powered by iPay Ghana" />
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
