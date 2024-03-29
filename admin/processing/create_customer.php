<?php 
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php' );
require_once( 'stripe-send-data.php' );
require_once( 'credit_card.php' );

if( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ) ) {

	$name = $_GET['name'];
	$email = $_GET['email'];
	$card_number = $_GET['card_number'];
	$code = $_GET['code'];
	$exp_month = $_GET['exp_month'];
	$exp_year = $_GET['exp_year'];
	
	try{
		if( empty( $name ) || empty( $email ) ){
			echo '<strong>ERROR: Both a name and email must be provided.</strong>';	
		}else{	
			if( !empty( $card_number ) ){
				$cust = create_customer( $name, $email, new CreditCard( $card_number, $code, $exp_month, $exp_year ) );
			}else{
				$cust = create_customer( $name, $email, null );
			}
			if( $cust ){
				echo '<strong>SUCCESS: Customer '.$name.' created successfully.</strong>';
			}
		}	
	}catch( Stripe_InvalidRequestError $exception ){
		echo '<strong>ERROR: '.$exception->getMessage().'</strong>';
	}catch( Stripe_CardError $exception ){
		echo '<strong>ERROR: '.$exception->getMessage().'</strong>';
	}
	
} else {

	echo 'Access Denied';

}

?>
