<?php

// Activate the WP_List_Table class because it is not activated automatically
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
require_once( 'stripe-processing.php' );

class Customers_Table extends WP_List_Table {

	function get_columns(){
		$columns = array(
						'customer' => 'Customer',
						'account' => 'Account',
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
		    	if( !empty( $item->name ) ){
		    	return '<a href="'.get_admin_url().'admin.php?page=dba_stripe_customer_detail&customer_id='.$item->id.'" title="Show Details">'.$item->name.'</a>';
		    	}else{
			    	return '<a href="'.get_admin_url().'admin.php?page=dba_stripe_customer_detail&customer_id='.$item->id.'" title="Show Details">NA</a>';
		    	}
		    case 'email':
		    	if( !empty( $item->email ) ) {
			    	return $item->email;
		    	}else{
			    	return 'N/A';
		    	}
		    	
		    case 'balance':
		    	return money_format( '%(#5n', ( $item->account_balance / 100 ) );
		    case 'delinquent':
		    	if( $item->delinquent != null ){
			    	return $item->delinquent;
		    	}else{
			    	return 'No';
		    	}
		    case 'details':
		    	return '<a class="button-secondary" href="'.get_admin_url().'admin.php?page=dba_stripe_customer_detail&customer='.$item->id.'" title="Show Details">Show Details</a>';
		    case 'account':
		    	return $item->id;
		}
    }
    
    function column_customer( $item ) {
	    $actions = array(
	    	'view' => sprintf('<a href="'.get_admin_url().'admin.php?page=dba_stripe_customer_detail&customer='.$item->id.'" title="View">View</a>'),
	    	'edit' => sprintf('<a href="?page=%s&action=%s&customer=%s">Edit</a>','dba_stripe_edit_customer','edit',$item->id),
	    	'delete' => sprintf('<a href="?page=%s&action=%s&customer=%s">Delete</a>',$_REQUEST['page'],'delete',$item->id),
	    );
	    return sprintf('%1$s %2$s', '<a href="'.get_admin_url().'admin.php?page=dba_stripe_customer_detail&customer='.$item->id.'" title="Show Details"><strong>'.$item->name.'</strong></a>', $this->row_actions($actions) );
    }

	function prepare_items() {
		
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = array();
		$this->_column_headers = array($columns, $hidden, $sortable);
		$per_page = 20;
		
		$current_page = $this->get_pagenum();
		$total_items = get_customer_count();
		$data = get_customers_by_page( $current_page, $per_page );
	 
		// Set pagination data for the page
		$this->set_pagination_args( 
			array( 'total_items' => $total_items, 'per_page' => $per_page ) );
		
		$this->items = $data;
	}
	

}

