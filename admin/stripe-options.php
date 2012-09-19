<?php
/**
 * Create Options Fields
 *
 * @since 0.1
 *
 */

require_once( 'stripe-options-interface.php' );

add_action('admin_init', 'sampleoptions_init_fn' );
// Register our settings. Add the settings section, and settings fields
function sampleoptions_init_fn(){
	register_setting('plugin_options', 'plugin_options', 'plugin_options_validate' );
	add_settings_section('main_section', 'Main Settings', 'section_text_fn', __FILE__);
	add_settings_field('plugin_text_string', 'Text Input', 'setting_string_fn', __FILE__, 'main_section');
	add_settings_field('plugin_text_pass', 'Password Text Input', 'setting_pass_fn', __FILE__, 'main_section');
	add_settings_field('plugin_textarea_string', 'Large Textbox!', 'setting_textarea_fn', __FILE__, 'main_section');
	add_settings_field('plugin_chk2', 'A Checkbox', 'setting_chk2_fn', __FILE__, 'main_section');
	add_settings_field('radio_buttons', 'Select Shape', 'setting_radio_fn', __FILE__, 'main_section');
	add_settings_field('drop_down1', 'Select Color', 'setting_dropdown_fn', __FILE__, 'main_section');
	add_settings_field('plugin_chk1', 'Restore Defaults Upon Reactivation?', 'setting_chk1_fn', __FILE__, 'main_section');
}

register_activation_hook(__FILE__, 'add_defaults_fn');
// Define default option settings
function add_defaults_fn() {
    $arr = array("dropdown1"=>"Orange", "text_area" => "Space to put a lot of information here!", "text_string" => "Some sample text", "pass_string" => "123456", "chkbox1" => "", "chkbox2" => "on", "option_set1" => "Triangle");
    update_option('plugin_options', $arr);
}

if ( ! function_exists( 'dba_stripe_add_options' ) ) {
	function dba_stripe_add_options() {
	
		
		// Add Top-Level Menu to WordPress
		add_menu_page(
			'DBA Commerce', 
			'DBA Commerce', 
			'administrator', 
			'dba_stripe_menu',
			'dba_stripe_show_main_menu');
		
		if( check_live_keys_exist() || check_test_keys_exist() ){	

			// Add Submenu to the Top-Level Menu
			add_submenu_page(
				'dba_stripe_menu',
				'Transfer History',
				'Transfer History',
				'administrator',
				'dba_stripe_transfer_history',
				'dba_stripe_show_transfer_history'
			);	
			
			add_submenu_page(
				null,
				'Transfer Detail',
				'Transfer Detail',
				'administrator',
				'dba_stripe_transfer_detail',
				'dba_stripe_show_transfer_detail'
			);
			
			add_submenu_page(
				null,
				'Edit Customer',
				'Edit Customer',
				'administrator',
				'dba_stripe_edit_customer',
				'dba_stripe_show_edit_customer'
			);
			
			add_submenu_page(
				null,
				'Customer Profile Detail',
				'Customer Profile Detail',
				'administrator',
				'dba_stripe_customer_detail',
				'dba_stripe_show_customer_detail'
			);
			
			$hook = add_submenu_page(
				'dba_stripe_menu',
				'Customers',
				'Customers',
				'administrator',
				'dba_stripe_customers',
				'dba_stripe_show_customers'
			);
			
			add_submenu_page(
				'dba_stripe_menu',
				'Transaction History',
				'Transaction History',
				'stripe_view',
				'dba_stripe_charge_history_view_only',
				'dba_stripe_show_charge_history_view_only'
			);
			
			add_submenu_page( 
				'dba_stripe_menu',
				'Create Customer',
				'Create Customer',
				'administrator',
				'dba_stripe_add_customer',
				'dba_stripe_show_add_customer'
			);

		}
		
		add_submenu_page(
			'dba_stripe_menu',
			'Settings',
			'Settings',
			'administrator',
			'dba_stripe_settings',
			'dba_stripe_show_settings'
		);

	}
}


if( ! function_exists( 'dba_stripe_options_init' ) ) {
	function dba_stripe_options_init(){

		/* 
			The register_setting f(n) defines an options group and its group name. The group
			is referenced in the section callback to notify the page how to set up the 
			following settings. The group is referenced to set up the options page and the
			group name (2nd param.) is used to store the options settings in the DB and 
			to retrieve them.
		*/	 
		register_setting( 'api_key_settings_group', 'api_key_settings' );

		/*
			All settings sections that are referenced on an options page have a shared
			heading but are separated by subheadings which are defined in the title
			attribute (2nd param.). The api_key_section_callback f(n) is used to draw the
			section heading and draws another div around its enclosed options/settings. The 
			page (last param.) is used to reference the section page (NOT the slug of the 
			menu that the section is being used on - a common falacy). The page name is 
			referenced in the do_settings_section f(n) mentioned in the options page callback. 
		*/
		add_settings_section( 'api_key_section', 'API Key Settings', 'api_key_section_callback', 'api_key_settings_page' );

		/*
			Used to add a field to the section. It does not draw the setting because that 
			is done in the callback. The title is used as a description in the beginning of 
			the field display. The callback is used to output the markup for the field itself. 
			The page is the section page which is defined in the add_settings_section f(n).   
		*/
		add_settings_field( 'api_key_mode', 
							'API Key Mode', 
							'api_key_mode_callback', 
							'api_key_settings_page', 
							'api_key_section' );
		
		add_settings_field( 'api_key_live_secret', 
							'Live Secret API Key', 
							'api_key_live_secret_callback', 
							'api_key_settings_page', 
							'api_key_section' );
							
		add_settings_field( 'api_key_live_publishable', 
							'Live Publishable API Key',
							'api_key_live_publishable_callback',
							'api_key_settings_page',
							'api_key_section' );
							
		add_settings_field( 'api_key_test_secret', 
							'Test Secret API Key', 
							'api_key_test_secret_callback', 
							'api_key_settings_page', 
							'api_key_section' );
							
		add_settings_field( 'api_key_test_publishable', 
							'Test Publishable API Key',
							'api_key_test_publishable_callback',
							'api_key_settings_page',
							'api_key_section' );
		

	}
}

if( ! function_exists( 'api_key_section_callback' ) ) {
	function api_key_section_callback(){
		?>
		
		
		<?php
	}
}

if( ! function_exists( 'api_key_mode_callback' ) ){
	function api_key_mode_callback(){
		?>	
		<?php $options = get_option( 'api_key_settings' ); ?>
		<?php 
		if( isset( $options['api_key_mode'] ) ) { 
			if( !empty( $options['api_key_mode'] ) ) { ?>
		<input type="radio" name="api_key_settings[api_key_mode]" value="live"<?php checked( 'live' == $options['api_key_mode'] ); ?> /> Live
		<input type="radio" name="api_key_settings[api_key_mode]" value="test"<?php checked( 'test' == $options['api_key_mode'] ); ?> /> Test
		<?php }else { ?>
			<input type="radio" name="api_key_settings[api_key_mode]" value="live" /> Live
			<input type="radio" name="api_key_settings[api_key_mode]" value="test" /> Test		
		<?php }
		}else{ ?>
			<input type="radio" name="api_key_settings[api_key_mode]" value="live" /> Live
			<input type="radio" name="api_key_settings[api_key_mode]" value="test" /> Test	
		<?php }
	}
}

if( ! function_exists( 'api_key_live_secret_callback' ) ) {
	function api_key_live_secret_callback(){
		$options = get_option( 'api_key_settings' );
		$output = "<input id='api_key_live_secret' name='api_key_settings[api_key_live_secret]' type='text' size='40' value='";
		if( isset( $options['api_key_live_secret'] ) ){
			if( !empty( $options['api_key_live_secret'] ) ) {
				$output .= $options['api_key_live_secret']."' />\n";
			}else{
				$output .= "' />\n";
			}
		}else{
			$output .= "' />\n";
		}
		echo $output;
	}
}

if( !  function_exists( 'api_key_live_publishable_callback' ) ) {
	function api_key_live_publishable_callback(){
		$options = get_option( 'api_key_settings' );
		$output = "<input id='api_key_live_publishable' name='api_key_settings[api_key_live_publishable]' type='text' size='40' value='";
		if( isset( $options['api_key_live_publishable'] ) ){
			if( !empty( $options['api_key_live_publishable'] ) ) {
				$output .= $options['api_key_live_publishable']."' />\n";
			}else{
				$output .= "' />\n";
			}
		}else{
			$output .= "' />\n";
		}
		echo $output;
	}
}

if( ! function_exists( 'api_key_test_secret_callback' ) ) {
	function api_key_test_secret_callback(){
		$options = get_option( 'api_key_settings' );
		$output = "<input id='api_key_test_secret' name='api_key_settings[api_key_test_secret]' type='text' size='40' value='";
		if( isset( $options['api_key_test_secret'] ) ){
			if( !empty( $options['api_key_test_secret'] ) ) {
				$output .= $options['api_key_test_secret']."' />\n";
			}else{
				$output .= "' />\n";
			}
		}else{
			$output .= "' />\n";
		}
		echo $output;
	}
}

if( !  function_exists( 'api_key_test_publishable_callback' ) ) {
	function api_key_test_publishable_callback(){
		$options = get_option( 'api_key_settings' );
		$output = "<input id='api_key_test_publishable' name='api_key_settings[api_key_test_publishable]' type='text' size='40' value='";
		if( isset( $options['api_key_test_publishable'] ) ){
			if( !empty( $options['api_key_test_publishable'] ) ) {
				$output .= $options['api_key_test_publishable']."' />\n";
			}else{
				$output .= "' />\n";
			}
		}else{
			$output .= "' />\n";
		}
		echo $output;
	}
}
