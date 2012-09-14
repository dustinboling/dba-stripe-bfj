<?php
/*
Plugin Name: DBA Stripe
Plugin URI: http://dustinboling.com/
Description: Stripe.com account management.
Author: Dustin Boling Associates
Version: 0.1
Author URI: http://dustinboling.com/
*/

// Constants
// ----------------------------------------------------------------------------------------------------------
define ( 'DBA_STRIPE_VERSION', '0.1' );
define ( 'DBA_STRIPE_PATH',  WP_PLUGIN_URL . '/' . end( explode( DIRECTORY_SEPARATOR, dirname( __FILE__ ) ) ) );


// Load PHP Lib - https://github.com/stripe/stripe-php
// ----------------------------------------------------------------------------------------------------------
if ( !class_exists( 'Stripe' ) ) {
    require_once( 'stripe-php/lib/Stripe.php' );
}
require_once( 'admin/stripe-options.php' );


// Register Settings ( & Defaults )
// ----------------------------------------------------------------------------------------------------------
if (get_option('dba_stripe_options')== '') {
    register_activation_hook(__FILE__, 'dba_stripe_defaults');
}

function dba_stripe_defaults() {

    $arr = array(
        'stripe_header' => 'Donate',
        'stripe_css_switch' => 'Yes',
        'stripe_api_switch'=>'Yes',
        'stripe_recent_switch'=>'Yes',
        'stripe_modal_ssl'=>'No'
    );

    update_option('dba_stripe_options', $arr);

}

function add_parameter_transfer_id( $qvars )
{
	$qvars[] = 'transfer_id';
	return $qvars;
}
add_filter('query_vars', 'add_parameter_transfer_id' );

function add_parameter_customer_id( $qvars )
{
	$qvars[] = 'customer_id';
	return $qvars;
}
add_filter('query_vars', 'add_parameter_customer_id' );


// Modify Subscriber role to be able to access part of the Stripe Plugin interface.
// ----------------------------------------------------------------------------------------------------------
function add_subscriber_stripe_view_capability(){
	$sub_role = get_role( 'subscriber' );
	$sub_role->add_cap( 'stripe_view' );
}
add_action( 'admin_init', 'add_subscriber_stripe_view_capability' );


// Extend user profiles to store stripe plugin data
// ----------------------------------------------------------------------------------------------------------

// Show stripe account field in the user profiles.
function show_stripe_account_field( $user ) { ?>
	<?php if( current_user_can( 'administrator' ) ) { ?>

		<h3>DBA Commerce Plugin</h3>
	
		<table class="form-table">
	
			<tr>
				<th><label for="stripe_account">Stripe Account</label></th>
				<td>
					<input type="text" 
						   name="stripe_account" 
						   id="stripe_account" 
						   value="<?php echo esc_attr( get_the_author_meta( 'stripe_account', $user->ID ) ); ?>" 
						   class="regular-text" />
					<br>
				</td>
			</tr>
	
		</table>
	<?php } 
}
add_action( 'show_user_profile', 'show_stripe_account_field' );
add_action( 'edit_user_profile', 'show_stripe_account_field' );

function my_save_extra_profile_fields( $user_id ) {

	if ( !current_user_can( 'manage_options', $user_id ) )
		return false;

	/* Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. */
	update_usermeta( $user_id, 'stripe_account', $_POST['stripe_account'] );
}
add_action( 'personal_options_update', 'my_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'my_save_extra_profile_fields' );





// Actions (Overview)
// ----------------------------------------------------------------------------------------------------------
add_action( 'admin_menu', 'dba_stripe_add_options' );
add_action( 'admin_init', 'dba_stripe_options_init' );

//add_action('wp_print_styles', 'load_dba_stripe_css');
//add_action('wp_print_scripts', 'load_dba_stripe_js');

//add_action('admin_print_styles', 'load_dba_stripe_admin_css');
//add_action('admin_print_scripts', 'load_dba_stripe_admin_js');


// JS & CSS
// ----------------------------------------------------------------------------------------------------------
function load_dba_stripe_js() {
    wp_enqueue_script( 'stripe-js', 'https://js.stripe.com/v1/', array('jquery') );
    wp_enqueue_script( 'dba-stripe-js', DBA_STRIPE_PATH . '/js/dba-stripe.js', array('jquery') );
    wp_localize_script( 'dba-stripe-js', 'wpstripekey', DBA_STRIPE_KEY );
    wp_localize_script( 'dba-stripe-js', 'ajaxurl', admin_url( 'admin-ajax.php' ) );
}

function load_dba_stripe_admin_js() {
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-tabs');
}

function load_dba_stripe_css() {
    $options = get_option('dba_stripe_options');
    if ( $options['stripe_css_switch'] ) {
        if ( $options['stripe_css_switch'] == 'Yes') {
            wp_enqueue_style('stripe-payment-css', DBA_STRIPE_PATH . '/css/dba-stripe-display.css');
        }
    }
    wp_enqueue_style('stripe-widget-css', DBA_STRIPE_PATH . '/css/dba-stripe-widget.css');
}

function load_dba_stripe_admin_css() {
    wp_enqueue_style('stripe-css', DBA_STRIPE_PATH . '/css/dba-stripe-admin.css');
}