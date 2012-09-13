<?php

// Activate the WP_List_Table class because it is not activated automatically
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
require_once( 'stripe-retrieve-data.php' );

class Customers_Table extends WP_List_Table {

	function get_columns(){
		$columns = array(
						'customer' => 'Customer',
						'email' => 'Email',
						'balance' => 'Balance',
						'delinquent' => 'Delinquent',
						'details' => 'Details'
						);
		return $columns;
	}
	
	function column_default( $item, $column_name ) {
		setlocale(LC_MONETARY, 'en_US');
	    switch( $column_name ) {	    
		    case 'customer':
		    	return $item->name;
		    case 'email':
		    	return $item->email;
		    case 'balance':
		    	return money_format( '%(#5n', ( $item->account_balance / 100 ) );
		    case 'delinquent':
		    	if( $item->delinquent != null ){
			    	return $item->delinquent;
		    	}else{
			    	return 'No';
		    	}
		    case 'details':
		    	return '<a class="button-secondary" href="'.get_admin_url().'admin.php?page=dba_stripe_customer_detail&customer_id='.$item->id.'" title="Show Details">Show Details</a>';
		}
    }

	function prepare_items() {
	
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = array();
		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->items = get_all_customers();
	}
	

}

