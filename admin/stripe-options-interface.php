<?php 

//Our class extends the WP_List_Table class, so we need to make sure that it's there
require_once( 'stripe-transactions-table.php' );
require_once( 'stripe-transfers-table.php' );
require_once( 'stripe-customers-table.php' );
require_once( 'stripe-user-charges.php' );

if( ! function_exists( 'dba_stripe_show_main_menu' ) ) {
	function dba_stripe_show_main_menu(){
		
		echo '<div class="wrap"><h2>DBA Commerce</h2><br><br>';
		echo '<a href="'.get_admin_url().'admin.php?page=dba_stripe_customers">Customers</a><br>';
		echo '<a href="'.get_admin_url().'admin.php?page=dba_stripe_transfer_history">Transfer History</a>';
		echo '</div>';
		show_dba_stripe_footer();
	}
}

if( ! function_exists( 'dba_stripe_show_transfer_history' ) ) {
	function dba_stripe_show_transfer_history(){
		
		echo '<div class="wrap"><h2>Transfer History</h2>';
		$options = get_option( 'api_key_settings' );
		if( isset( $options['api_key_mode'] ) ){
			if( $options['api_key_mode'] == 'live' ){
				if( !empty( $options['api_key_live_secret'] ) && !empty( $options['api_key_live_publishable'] ) ){
					$myListTable = new Transfers_Table();
					$myListTable->prepare_items();
					$myListTable->display();		
				}else{
					echo '<br>Both Live API Keys are required. Please enter both keys in the settings panel.';
				}
			}else{
				if( !empty( $options['api_key_test_secret'] ) && !empty( $options['api_key_test_publishable'] ) ){
					$myListTable = new Transfers_Table();
					$myListTable->prepare_items();
					$myListTable->display();		
				}else{
					echo '<br>Both Test API Keys are required. Please enter both keys in the settings panel.';
				}
			}
		}else{
			echo '<br>No API keys found! Please enter them in the settings panel of DBA Commerce. The API keys are found on Stripe.com. Log into your account and you will find them in your account settings menu.';
		}
		echo '</div>'; 
		show_dba_stripe_footer();	
	}
}

if( ! function_exists( 'dba_stripe_show_transfer_detail' ) ) {
	function dba_stripe_show_transfer_detail(){
		setlocale(LC_MONETARY, 'en_US');
		echo '<div class="wrap"><h2>';
		
		$options = get_option( 'api_key_settings' );
		if( isset( $options['api_key_mode'] ) ){
			if( $options['api_key_mode'] == 'live' ){
				if( !empty( $options['api_key_live_secret'] ) && !empty( $options['api_key_live_publishable'] ) ){
					$show = true;	
				}else{
					echo '<br>Both Live API Keys are required. Please enter both keys in the settings panel.';
				}
			}else{
				if( !empty( $options['api_key_test_secret'] ) && !empty( $options['api_key_test_publishable'] ) ){
					$show = true;	
				}else{
					echo '<br>Both Test API Keys are required. Please enter both keys in the settings panel.';
				}
			}
		}else{
			echo '<br>No API keys found! Please enter them in the settings panel of DBA Commerce. The API keys are found on Stripe.com. Log into your account and you will find them in your account settings menu.';
		}
		
		if( $show ) {
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
				echo '<br>No transfer ID was provided.';
			}		
		}
		echo '</div>'; 
		show_dba_stripe_footer();	
	}
}

if( ! function_exists( 'dba_stripe_show_customers' ) ) {
	function dba_stripe_show_customers(){
		?>
			<div class='wrap'>
				<div id="icon-tools" class="icon32"></div>
				<h2>Customers</h2>
				<?php
					$options = get_option( 'api_key_settings' );
					if( isset( $options['api_key_mode'] ) ){
						if( $options['api_key_mode'] == 'live' ){
							if( !empty( $options['api_key_live_secret'] ) && !empty( $options['api_key_live_publishable'] ) ){
								$customers_table = new Customers_Table();
								$customers_table->prepare_items();
								$customers_table->display();	
							}else{
								echo '<br>Both Live API Keys are required. Please enter both keys in the settings panel.';
							}
						}else{
							if( !empty( $options['api_key_test_secret'] ) && !empty( $options['api_key_test_publishable'] ) ){
								$customers_table = new Customers_Table();
								$customers_table->prepare_items();
								$customers_table->display();	
							}else{
								echo '<br>Both Test API Keys are required. Please enter both keys in the settings panel.';
							}
						}
					}else{
						echo '<br>No API keys found! Please enter them in the settings panel of DBA Commerce. The API keys are found on Stripe.com. Log into your account and you will find them in your account settings menu.';
					}				
				?>
			</div>
		<?php	
	}
}

if( ! function_exists( 'dba_stripe_show_charge_history_view_only' ) ) {
	function dba_stripe_show_charge_history_view_only(){
		$current_user = wp_get_current_user();
		?>
		<div class='wrap'>
			<h2>Charge History</h2>
			<?php
			// Get user account from user profile
			$stripe_acct = get_current_user_stripe_account();
			
			try{
				$cust = get_customer( $stripe_acct );
				
				echo '<div id="message" class="updated"><br>';
				echo '<strong>Name: </strong>'.$current_user->display_name.'<br>';
				echo '<strong>Account: </strong>'.$cust->id.'<br>';
				echo '<br></div>';
				$charge_table = new User_Charges_Table( $cust->id );
				$charge_table->prepare_items();
				$charge_table->display();
			
			}catch( Stripe_InvalidRequestError $exception){
				echo '<div id="message" class="updated">';
				echo '<br><strong>ERROR: '.$exception->getMessage().'</strong><br><br>';
				echo '</div>';
			}
			
			?>
		</div>
		<?php	
	}	
}

if( ! function_exists( 'dba_stripe_show_add_customer' ) ) {
	function dba_stripe_show_add_customer(){
		?>
			<div class='wrap'>
				<h2>Create Customer</h2>
				<form action="javascript:void(0);">
				
					<ul>
				
						<li>
							<label>Name: </label>
							<input type="text" 
								   name="customer_name"
								   id="customer_name" />
						</li>
							<label>Email: </label>
							<input type="text" 
								   name="customer_email"
								   id="customer_email" />
						</li>
						<br><br>
						<li>
							<input class="submit-button"
								   type="submit"
								   value="Create Customer" />
						
						</li>
						
					</ul>
				
				</form>	
			</div>
			<script>
				jQuery(function() {
					jQuery(".submit-button").click(function(){
						var name = jQuery("#customer_name").val();
						alert("Hi " + name);
					})
				})
			
			
			
			</script>
			
		<?
	}
}

if( ! function_exists( 'dba_stripe_show_settings' ) ) {
	function dba_stripe_show_settings(){
		?>
			<div class='wrap'>
				<div id="icon-tools" class="icon32"></div>
				<h2>DBA Commerce Settings</h2>
				<form method='post' action='options.php'>
					<?php
						settings_fields( 'api_key_settings_group' );
						do_settings_sections( 'api_key_settings_page' );
					?>
					<p class='submit'>
						<input name='submit' type='submit' class='button-primary' value='<?php _e( "Save Changes" ) ?>' />
					</p>
				</form>	
			</div>
		<?
	}
}

if( ! function_exists( 'show_dba_stripe_footer' ) ){
	function show_dba_stripe_footer(){
		echo '<div style="position: absolute; bottom: 0px;">Powered by <a href="http://www.dustinboling.com">DBA Commerce</a> Designed by <a href="http://www.dustinboling.com">Dustin Boling Associates</a>';
		echo '</div>';	
	}
}