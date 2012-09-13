<?php

require_once( 'business_objects/customer.php' );
require_once( 'business_objects/charge.php' );
require_once( 'business_objects/basic_charge.php' );
require_once( 'business_objects/event.php' );
Stripe::setApiKey("tqGmZrDeX4JmYmJbjnmKipyQe7z96IRV");

function get_customers(){
	$data = Stripe_Customer::all();
	$raw_customers = $data->data;
	
	$customers = null;
	foreach( $raw_customers as $raw_cust ) {
		$customers[] = new Customer( $raw_cust );
	}
	
	return $customers;
}

function get_transfers() {
	$transfers = Stripe_Transfer::all( array( 'count' => 100 ) );

	return $transfers->data;
}

function get_transfer ( $transfer_id ) {
	$transfer = Stripe_Transfer::retrieve( $transfer_id );
	return $transfer;
	//return $transfers->data;
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