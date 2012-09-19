<?php



/* 
Get the API Key from the settings in the DB. The type of key (whether live or test)
is dependent upon the setting in the options page. 
*/

$opt = get_option( 'api_key_settings' );
if( isset( $opt ) ){
	if( isset( $opt['api_key_mode'] ) ) {
		if( $opt['api_key_mode'] == 'live' ){
			Stripe::setApiKey( trim( $opt['api_key_live_secret'] ) );
		}
		if( $opt['api_key_mode'] == 'test' ){
			Stripe::setApiKey( trim( $opt['api_key_test_secret'] ) );
		}
	}
}

function create_customer( $customer_name, $customer_email, $card=null ){
	if( $card ){
		$cust = Stripe_Customer::create( 
			array( 'description' => $customer_name, 'email' => $customer_email, 
				   'card' => array(
				   					'number' => $card->get_card_number(),
				   					'exp_month' => $card->get_exp_month(),
				   					'exp_year' => $card->get_exp_year(),
				   					'cvc' => $card->get_cvc() ) ) );
	}else{
		$cust = Stripe_Customer::create( 
			array( 'description' => $customer_name, 'email' => $customer_email ) );
	}
	return $cust;
}

function update_customer( $customer_id, $customer_name, $customer_email, $card=null ){
	$existing_customer = Stripe_Customer::retrieve( $customer_id );
	$existing_customer->description = $customer_name;
	$existing_customer->email = $customer_email;
	if( $card ){
		$new_card = array(
	   					'number' => $card->get_card_number(),
	   					'exp_month' => $card->get_exp_month(),
	   					'exp_year' => $card->get_exp_year(),
	   					'cvc' => $card->get_cvc() 
	   					 );
		$existing_customer->card = $new_card;
	}
	
	return $existing_customer->save();
}