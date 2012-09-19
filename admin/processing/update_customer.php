<?php 
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php' );
require_once( 'stripe-send-data.php' );
require_once( 'credit_card.php' );

if( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ) ) {

	$id = $_GET['id'];
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
				$cust = update_customer( $id, $name, $email, new CreditCard( $card_number, $code, $exp_month, $exp_year ) );
			}else{
				$cust = update_customer( $id, $name, $email );
			}
			if( $cust ){
				if( !empty($cust->active_card->cvc_check) && ($cust->active_card->cvc_check != 'fail' ) ){
					echo '<strong>SUCCESS: Customer '.$name.' updated successfully.</strong>num:'.$cust->active_card->last4.'type:'.$cust->active_card->type;
				}else{
					echo '<strong>ERROR: CVC code is not valid.</strong>';	
				}
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
