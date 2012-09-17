<?php

// Activate the WP_List_Table class because it is not activated automatically
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
require_once( 'stripe-processing.php' );

class Transfers_Table extends WP_List_Table {

	function get_columns(){
		$columns = array(
						'date' => 'Date',
						'status' => 'Status',
						'amount' => 'Amount',
						'fees' => 'Fees',
						'proceeds' => 'Proceeds',
						'details' => 'Details'
						);
		return $columns;
	}
	
	function column_default( $item, $column_name ) {
	    switch( $column_name ) {
		    case 'date':
		    	return '<a href="'.get_admin_url().'admin.php?page=dba_stripe_transfer_detail&transfer_id='.$item->id.'" title="Show Details">'.date("n/j/Y", $item->date).'</a>';
		    case 'amount':
		    	setlocale(LC_MONETARY, 'en_US');
		    	return money_format( '%(#5n', ( $item->summary->charge_gross / 100 ) );
		    case 'details':
		    	return '<a class="button-secondary" href="'.get_admin_url().'admin.php?page=dba_stripe_transfer_detail&transfer_id='.$item->id.'" title="Show Details">Show Details</a>';
		    case 'status':
		    	return ucfirst($item->$column_name);
		    case 'fees':
		    	return money_format( '%(#5n', ( $item->summary->charge_fees / 100 ) );	    
		    case 'proceeds':
		    	return money_format( '%(#5n', ( $item->amount / 100 ) );	    
		}
    }

	function prepare_items() {
	
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = array();
		$this->_column_headers = array($columns, $hidden, $sortable);
		
		$per_page = 25;
		$current_page = $this->get_pagenum();
		$total_items = get_transfer_count();
		$data = get_transfers_by_page( $current_page, $per_page );
	 
		// Set pagination data for the page
		$this->set_pagination_args( 
			array( 'total_items' => $total_items, 'per_page' => $per_page ) );
		
		$this->items = $data;
				
	}
	

}

