<?php

require_once( 'business_objects/customer.php' );
require_once( 'business_objects/charge.php' );
require_once( 'business_objects/basic_charge.php' );
require_once( 'business_objects/event.php' );

/* 
Get the API Key from the settings in the DB. The type of key (whether live or test)
is dependent upon the setting in the options page. 
*/
$opt = get_option( 'api_key_settings' );
if( $opt['api_key_mode'] == 'live' ){
	Stripe::setApiKey( trim( $opt['api_key_live_secret'] ) );
}
if( $opt['api_key_mode'] == 'test' ){
	Stripe::setApiKey( trim( $opt['api_key_test_secret'] ) );
}
$opt = null;

/* Returns up to 100 of the most recent customers */
function get_customers(){
	$data = Stripe_Customer::all( array( 'count' => 100 ) );
	$raw_customers = $data->data;
	
	$customers = null;
	if( count( $raw_customers ) > 0 ){
		foreach( $raw_customers as $raw_cust ) {
			$customers[] = new Customer( $raw_cust );
		}
	}
	
	return $customers;
}

function get_all_customers( $chunk_size = 100 ){
	
	// Initially set count_offset to 0 because there may be no need to 
	// do any further iterations (less than 100 customers are in the
	// system.
	$count_offset = 0;
	
	$customers = null;
	$current_chunk = null;
	do{
		$current_chunk = Stripe_Customer::all(
			array( 'count' => $chunk_size, 'offset' => $count_offset ) );
		
	
		if( count( $current_chunk->data ) > 0 ){
			foreach( $current_chunk->data as $cust ){
				$customers[] = new Customer( $cust );
			}
		}
	
		if( count( $customers ) < $current_chunk->count ){
			$count_offset = $count_offset + $chunk_size;
		}
	
	}while( count( $customers ) < $current_chunk->count );
	
	return $customers;
} 

function get_customers_by_page( $page, $chunk_size = 100 ){
	$cust_data = Stripe_Customer::all( 
		array( 'count' => $chunk_size, 'offset' => ( ( $page - 1) * $chunk_size ) ) );
	$raw_customers = $cust_data->data;
	
	$customers = null;
	if( count( $raw_customers ) > 0 ){
		foreach( $raw_customers as $raw_cust ) {
			$customers[] = new Customer( $raw_cust );
		}
	}
	return $customers;
}

function get_customer_count(){
	$cust_data = Stripe_Customer::all( array( 'count' => 1 ) );
	return $cust_data->count;
}

/* Returns up to 100 of the most recent transfers */
function get_transfers() {
	$transfers = Stripe_Transfer::all( array( 'count' => 100 ) );

	return $transfers->data;
}

function get_all_transfers( $chunk_size = 100 ){
	
	// Initially set count_offset to 0 because there may be no need to 
	// do any further iterations (less than 100 transers are in the
	// system.
	$count_offset = 0;
	
	$transfers = null;
	$current_chunk = null;
	do{
		$current_chunk = Stripe_Transfer::all(
			array( 'count' => $chunk_size, 'offset' => $count_offset ) );
		
	
		if( count( $current_chunk->data ) > 0 ){
			foreach( $current_chunk->data as $trfr ){
				$transfers[] = $trfr;
			}
		}
	
		if( count( $transfers ) < $current_chunk->count ){
			$count_offset = $count_offset + $chunk_size;
		}
	
	}while( count( $transfers ) < $current_chunk->count );
	
	return $transfers;
} 

function get_transfers_by_page( $page, $chunk_size = 100 ){
	$trfr_data = Stripe_Transfer::all( 
		array( 'count' => $chunk_size, 'offset' => ( ( $page - 1) * $chunk_size ) ) );
		$raw_transfers = $trfr_data->data;
	
	$transfers = null;
	if( count( $raw_transfers ) > 0 ){
		foreach( $raw_transfers as $raw_trfr ) {
			$transfers[] = $raw_trfr;
		}
	}
	return $transfers;
}

function get_transfer ( $transfer_id ) {
	$transfer = Stripe_Transfer::retrieve( $transfer_id );
	return $transfer->data;
}

function get_transfer_count(){
	$trfr_data = Stripe_Transfer::all( array( 'count' => 1 ) );
	return $trfr_data->count;
}

function get_transactions_by_transfer( $transfer_id ){
	$transfer = Stripe_Transfer::retrieve( $transfer_id );

	$transactions = $transfer->transactions( array( 'count' => 100 ) );
	$transactions = $transactions->data;
	
	$charges = null;
	foreach( $transactions as $transaction ){
		$tmp_bc = new BasicCharge($transaction);
		$charges[] = new Charge( $tmp_bc );
	}
	
	return $charges;
}

function get_events_charge_refunded(){
	$cf_events = Stripe_Event::all( array( 'type' => 'charge.refunded' ) );
	$cf_events_array = $cf_events->data;
	$charge_refunded_events = null;
	foreach( $cf_events_array as $cf_event ){
		$charge_refunded_events[] =  new Event( $cf_event );
	}
	return $charge_refunded_events;
}

function get_events_charge_succeeded(){
	$cs_events = Stripe_Event::all( array( 'type' => 'charge.succeeded' ) );
	$cs_events_array = $cs_events->data;
	$charge_succeeded_events = null;
	foreach( $cs_events_array as $cs_event ){
		$charge_succeeded_events[] =  new Event( $cs_event );
	}
	return $charge_succeeded_events;
}


function get_transactions() {

	// The basic flow
	$transfers = Stripe_Transfer::all( array( 'count' => 100 ) );
	$transfers = $transfers->data;
	
	
	
	foreach( $transfers as $transfer ) {
		
		echo '<strong>Transferred $', number_format( $transfer->amount / 100, 2 ) , ' on ', Date( 'F jS, Y', $transfer->date ), '</strong><br>';
		// Get all of the transactions included in this transfer
		$transactions = $transfer->transactions( array( 'count' => '100' ) );
		$transactions = $transactions->data;
		
		foreach( $transactions as $transaction ) {
		
			// Get the charge that's related to this transaction
			$charge = Stripe_Charge::retrieve( $transaction->id );
			
			//if( $charge->paid ) {
				echo '$', number_format( $charge->amount / 100, 2 ), ( $charge->refunded ) ? ' REFUNDED to ' : ' donated by ';
				
				// Get the customer that's related to this transaction's charge
				$customer = Stripe_Customer::retrieve( $charge->customer );
				
				echo $customer->description, '<br>';
			//}
		}
		
		echo '<br>';
	}
	wp_die();

}


function get_charge( $id ) {
	$charge = Stripe_Charge::retrieve( $id );

	return $charge->data;
}

function get_customer( $id ) {
	$customer = Stripe_Customer::retrieve( $id );

	return $customer->data;
}