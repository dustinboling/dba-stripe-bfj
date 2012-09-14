<?php 
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php' );
require_once( 'stripe-send-data.php' );
//if( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ) ) {

	$name = $_GET['name'];
	$email = $_GET['email'];
	echo 'Create function reached!';
	create_customer( $name, $email);


//} else {

//	echo 'Access Denied';

//}


?>
