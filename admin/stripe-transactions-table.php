<?php

// Activate the WP_List_Table class because it is not activated automatically
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
require_once( 'stripe-retrieve-data.php' );

class Transactions_Table extends WP_List_Table {

	private $transfer_id;

	function get_columns(){
		$columns = array(
						'date' => 'Date',
						'customer' => 'Customer',
						'status' => 'Status',
						'amount' => 'Payment',
						'fee' => 'Fee',
						'proceeds' => 'Proceeds'
						);
		return $columns;
	}
	
	function column_default( $item, $column_name ) {
	    setlocale(LC_MONETARY, 'en_US');
	    
	    switch( $column_name ) {
		    case 'customer':
		    	if( $item->customer ){
			    	$customer = Stripe_Customer::retrieve( $item->customer );
					if( $customer ){
						return $customer->description;
					}else{
						return 'N/A';
					}
				}else{
					return 'N/A';
				}
				return 'N/A';
		    case 'status':
		    	if( $item->refunded ){
			    	return 'Refunded';
		    	}else{
		    		if( $item->refund_gross > 0){
			    		return 'Partially Refunded';
		    		}else{
			    		return 'Paid';
			    	}
		    	}
		    	return 'N/A';
		    case 'amount':
		    	$amt = $item->amount;
		    	return money_format( '%(#5n', ( $amt / 100 ) );
		    case 'date':
		    	return date( "n/j/Y", $item->created ); 
		    case 'fee':
		    	return money_format( '%(#5n', ( $item->$column_name / 100 ) );
		    case 'proceeds':
		    	return money_format( '%(#5n', ( $item->$column_name / 100 ) );
	    }
    }

	function prepare_items() {
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = array();
		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->items = get_transactions_by_transfer( $this->transfer_id );
	}
	
	function __construct( $id=null ){
		parent::__construct();
		if( $id != null){
			$this->transfer_id = $id;
		}
	}
}
