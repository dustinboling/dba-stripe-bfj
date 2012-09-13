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

if( ! function_exists( 'dba_stripe_options_init' ) ) {
	function dba_stripe_options_init(){
		 
		 register_setting( 'api_key_settings_group', 'api_key_settings' );
		 add_settings_section( 'api_key_section', 'API Key Settings', 'api_key_section_callback', 'api_key_settings_page' );
		 add_settings_field( 'plugin_text', 'Enter a name', 'field_callback', 'api_key_settings_page', 'api_key_section' );
	}
}

if( ! function_exists( 'api_key_section_callback' ) ) {
	function api_key_section_callback(){
		?>
		
		
		<?php
	}
}

if( ! function_exists( 'field_callback' ) ) {
	function field_callback(){
		$options = get_option( 'api_key_settings' );
		echo "<input id='plugin_text' name='api_key_settings[plugin_text]' type='text' value='{$options['plugin_text']}' />";
	}
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
			'dba_stripe_menu',
			'Settings',
			'Settings',
			'administrator',
			'dba_stripe_settings',
			'dba_stripe_show_settings'
		);

	}
}
