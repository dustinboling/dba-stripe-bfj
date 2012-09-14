<?php

// Activate the WP_List_Table class because it is not activated automatically
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
require_once( 'stripe-retrieve-data.php' );

class User_Charges_Table extends WP_List_Table {

	private $customer_id;

	function get_columns(){
		$columns = array(
						'date' => 'Date',
						'description' => 'Description',
						'status' => 'Status',
						'payment' => 'Payment',
						'refund' => 'Refund',
						'net_charge' => 'Final Payment'
						);
		return $columns;
	}
	
	function column_default( $item, $column_name ) {
	    setlocale(LC_MONETARY, 'en_US');
	    $net_charge = $item->amount - $item->amount_refunded;
	    
	    switch( $column_name ) {
		    case 'status':
		    	if( $item->refunded ){
			    	return 'Refunded';
		    	}else{
		    		
		    		if( ( $item->amount_refunded > 0) && ( $net_charge > 0 ) ) {
			    		return 'Partially Refunded';
		    		}elseif( $net_charge == 0 ){
			    		return 'Refunded';	
		    		}else{
			    		return 'Paid';
			    	}
		    	}
		    	return 'N/A';
		    case 'payment':
		    	return money_format( '%(#5n', ( $item->amount / 100 ) );
		    case 'date':
		    	return date( "n/j/Y", $item->created );
		    case 'description':
		    	if( !empty( $item->description ) ) {
			    	return $item->description;	
		    	}else{
			    	return 'N/A';
		    	}
		    case 'refund':
		    	return money_format( '%(#5n', ( $item->amount_refunded / 100 ) );
		    case 'net_charge':
		    	return money_format( '%(#5n', ( $net_charge / 100 ) );
	    }
    }

	function prepare_items() {
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		
		try{

			$per_page = 25;
			$current_page = $this->get_pagenum();
			$total_items = get_customer_charge_count();
			$data = get_charges_by_customer_by_page( $this->customer_id, $current_page, $per_page );
		 
			// Set pagination data for the page
			$this->set_pagination_args( 
				array( 'total_items' => $total_items, 'per_page' => $per_page ) );
			
			$this->items = $data;
		
		}catch( Stripe_InvalidRequestError $exception ){
			echo 'Error Found!';
		}






	}
	
	function __construct( $id=null ){
		parent::__construct();
		if( $id != null){
			$this->customer_id = $id;
		}
	}
}
