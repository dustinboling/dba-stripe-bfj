<?php 
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php' );
if( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ) ) {


} else {
	echo 'Access Denied';
}


?>
