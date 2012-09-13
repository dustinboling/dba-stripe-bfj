<?php 

//Our class extends the WP_List_Table class, so we need to make sure that it's there
require_once( 'stripe-transactions-table.php' );
require_once( 'stripe-transfers-table.php' );

if( ! function_exists( 'dba_stripe_show_main_menu' ) ) {
	function dba_stripe_show_main_menu(){
		
		echo '<div class="wrap"><h2>Donations</h2><br /><br />';
		echo '<a href="'.get_admin_url().'admin.php?page=dba_stripe_transfer_history">Transfer History</a>';
		
		echo '</div>';
		echo '<div style="position: absolute; bottom: 0px;">Powered by <a href="http://www.dustinboling.com">DBA Commerce</a> Designed by <a href="http://www.dustinboling.com">Dustin Boling Associates</a>';
		echo '</div>';
	}
}

if( ! function_exists( 'dba_stripe_show_transfer_history' ) ) {
	function dba_stripe_show_transfer_history(){
		
		echo '<div class="wrap"><h2>Transfer History</h2>';
		$myListTable = new Transfers_Table();
		$myListTable->prepare_items();
		$myListTable->display();
		echo '</div>'; 
		echo '<div style="position: absolute; bottom: 0px;">Powered by <a href="http://www.dustinboling.com">DBA Commerce</a> Designed by <a href="http://www.dustinboling.com">Dustin Boling Associates</a>';
		echo '</div>';		
	}
}

if( ! function_exists( 'dba_stripe_show_transfer_detail' ) ) {
	function dba_stripe_show_transfer_detail(){
		setlocale(LC_MONETARY, 'en_US');
		echo '<div class="wrap"><h2>';
		if ( isset( $_GET['transfer_id'] ) ) {
			$transfer_id = $_GET['transfer_id'];
			$transfer_data = get_transfer( $transfer_id );
			echo 'Transfer Detail</h2>';
			echo '<table style="margin-top: -35px; margin-bottom: -15px;">';
			if( !empty( $transfer_data->description ) ){
				echo '<tr><th align="left"><strong>Description: </strong></th><td colspan="3">'.$transfer_data->description.'</td></tr><br /></tr>';
			}
			echo '<tr><th align="left" width="100"><strong>Date: </strong></th><td width="100">'.date( "n/j/Y", $transfer_data->date ).'</td><br />';
			echo '<th align="left" width="100"><strong>Status: </strong></th><td>'.ucfirst( $transfer_data->status ).'</td></tr><br />';
			echo '<tr><th align="left"><strong>Gross Payment: </strong></th><td>'.trim( money_format( '%(#5n', ( $transfer_data->summary->charge_gross / 100 ) ) ).'</td>';
			echo '<th align="left"><strong>Refunds: </strong></th><td>'.trim( money_format( '%(#5n', ( $transfer_data->summary->refund_gross / 100 ) ) ).'</td></tr>';
			echo '<tr><th align="left"><strong>Fees: </strong></th><td>'.trim( money_format( '%(#5n', ( $transfer_data->summary->charge_fees / 100 ) ) ).'</td>';
			echo '<th align="left"><strong>Net Proceeds: </strong></th><td>'.trim( money_format( '%(#5n', ( $transfer_data->summary->net / 100 ) ) ).'</td></tr><br />';
			echo '<tr><th align="left"><strong>Charges: </strong></th><td>'.$transfer_data->summary->charge_count.'</td>';
			echo '<th align="left"><strong>Refunds: </strong></th><td>'.$transfer_data->summary->refund_count.'</td></tr>';
			echo '</table>';
			$transferTransactionsTable = new Transactions_Table( $transfer_id );
			$transferTransactionsTable->prepare_items();
			$transferTransactionsTable->display();
		}else{
			echo 'Transfer Detail</h2>';
			echo '<br />No transfer ID was provided.';
		}		
		
		echo '</div>'; 
		echo '<div style="position: absolute; bottom: 0px;">Powered by <a href="http://www.dustinboling.com">DBA Commerce</a> Designed by <a href="http://www.dustinboling.com">Dustin Boling Associates</a>';
		echo '</div>';		
	}
}
