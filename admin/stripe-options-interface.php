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

if( ! function_exists( 'dba_stripe_show_edit_customer' ) ) {
	function dba_stripe_show_edit_customer(){
		$customer = null;
		if( isset( $_GET['customer'] ) ) {
			if( customer_exists( $_GET['customer'] ) ){
				$customer = get_customer( $_GET['customer'] );	
			}else{
				
			}
		}		
		?>
		<style>
			fieldset {
				border: 1px solid #000000;
				width: 250px;
				padding-left: 20px;
				padding-bottom: 15px;
				-webkit-border-radius: 8px;
				-moz-border-radius: 8px;
				border-radius: 8px;
			}
			
			legend {
				color: #000000;
				padding: 2px;
			}
			
			.legend-text {
				font-weight: italic;
			}
			
			.FormInfo td {
				padding-top: 10px;
				padding-left: 15px;
			}
			
			.FormInfo th {
				padding-top: 10px;
				text-align: right;
			}
		</style>
		<div class='wrap'>
			<?php
			if( !empty( $customer ) ){
				echo '<h2>Edit '.$customer->description.'</h2>';
			}else{
				echo '<h2>Edit Customer</h2>';	
			}
			?>
			
			<div id="message" class="updated" style="display:none;"></div>
			<form action="javascript:void(0);">
				<div style="padding: 10px;">
					<fieldset style="width: 350px;">
						<legend>Name & Email</legend>
						<table class="FormInfo" >
							<tr>	
								<th>Name</th>
								<td>
									<input type="text" 
									   name="customer_name"
									   id="customer_name" 
									   size="35"
									   <?php echo 'value="'.$customer->description.'"'; ?>>
								</td>
							</tr>
							<tr>
								<th>Email</th>
								<td>
									<input type="text" 
									   	   name="customer_email"
									       id="customer_email" 
									       size="35"
									       <?php echo 'value="'.$customer->email.'"'; ?>>
								</td>
							</tr>
							
													
						</table>
					</fieldset>
					
					<fieldset style="margin-top: 15px; width: 350px;">
						<legend>Credit Card Information</legend>
						<table class="FormInfo">
							
							<?php if( !empty( $customer->active_card->last4 ) ) : ?>
								<tr>	
									<th>Current<br>Card Number</th>
									<td>
										<?php echo '****************'.$customer->active_card->last4; ?>
									</td>
								</tr>
							<?php endif; ?>
							
							<?php if( !empty( $customer->active_card->type ) ) : ?>
								<tr>	
									<th>Current<br>Card Type</th>
									<td>
										<?php echo $customer->active_card->type; ?>
									</td>
								</tr>
							<?php endif; ?>
							
							<tr>	
								<th>New Card<br>Number</th>
								<td>
									<input type="text" 
									   name="customer_card_number"
									   id="customer_card_number" 
									   size="25" >
								</td>
							</tr>
							<tr>
								<th>New CVC</th>
								<td>
									<input type="text"
										   name="customer_card_verification_code"
										   id="customer_card_verification_code" 
										   size="25" >
								</td>
							</tr>
							
							<tr>
								<th>Current<br>Expiration Date</th>
								<td>
									<select name="customer_card_exp_month"
											id="customer_card_exp_month"
											style="width: 50px;">
										<?php
											for( $j = 1; $j <= 12; $j++){
												if( $j <= 9){
													echo '<option';
													$month = '0'.$j;
													if( $customer->active_card->exp_month == ( $month ) ){
														echo ' selected="selected"';
													}													
													echo '>'.$month.'</option>';
												}else{
													echo '<option>'.$j.'</option>';
												}	
											}
										?>
									</select>
									
									<?php $curr_year = date('Y'); ?>
									<select name="customer_card_exp_year"
											id="customer_card_exp_year"
											style="width: 60px;">
										<?php
											for ( $i = 0; $i <= 10; $i++ ) {
											    echo '<option';
											    if( $customer->active_card->exp_year == ( $curr_year + $i ) ){
											    	echo ' selected="selected"';
											    }
											    echo '>'.( $curr_year + $i ).'</option>';
											}											
										?>
									</select>
								</td>
							</tr>
							<tr><th></th><td></td></tr>
							<tr>
								<td colspan="2" style="text-align: left;">*Change expiration date to modify</td>
							</tr>
						
						</table>
					</fieldset>
					
					<br>
					<input type="button" value="Back" onclick="history.back()">
					
					<input class="submit-button"
						   style="margin-left: 219px;"
						   type="submit"
						   value="Update Customer" >
						
				</div>
			</form>	
		</div>
		<?php	
	}
}

if( ! function_exists( 'dba_stripe_show_customers' ) ) {
	function dba_stripe_show_customers(){
		?>
			<div class='wrap'>
				<div id="icon-tools" class="icon32"></div>
				<h2>Customers</h2>
				<div id="message" class="updated" style="display:none;"></div>
				<?php
					$options = get_option( 'api_key_settings' );
					if( isset( $options['api_key_mode'] ) ){
						if( $options['api_key_mode'] == 'live' ){
							if( !empty( $options['api_key_live_secret'] ) && !empty( $options['api_key_live_publishable'] ) ){
								if( isset( $_GET['action'] ) && isset( $_GET['customer']  ) ){
									if( $_GET['action'] == 'delete' ){
										if( customer_exists( $_GET['customer'] ) ){
											$cust = get_customer( $_GET['customer'] );
											if( !$cust->deleted ){
												$del_cust = delete_customer( $_GET['customer']);
												if( $del_cust->deleted ){
													unset($_GET['action']);
													unset($_GET['customer']);
													?> 
														<script type="text/javascript">
															<?php if( !empty( $cust->description ) ) { ?>
															var del_message = "<br><strong>Customer "<?php echo ' + '.'"'.$cust->description.'"'; ?> + ' deleted successfully.</strong><br><br>';
															<?php }else{ ?>
																var del_message = 'Customer successfully deleted!';
															<?php } ?>
															jQuery(function() {
																
																jQuery('#message').html(del_message);
																jQuery('#message').fadeIn("slow").show();
															})
														</script>
													<?php
												}
											}
										}
									}	
								}
								$customers_table = new Customers_Table();
								$customers_table->prepare_items();
								$customers_table->display();	
							}else{
								echo '<br>Both Live API Keys are required. Please enter both keys in the settings panel.';
							}
						}else{
							if( !empty( $options['api_key_test_secret'] ) && !empty( $options['api_key_test_publishable'] ) ){
								if( isset( $_GET['action'] ) && isset( $_GET['customer']  ) ){
									if( $_GET['action'] == 'delete' ){
										if( customer_exists( $_GET['customer'] ) ){
											$cust = get_customer( $_GET['customer'] );
											if( !$cust->deleted ){
												$del_cust = delete_customer( $_GET['customer']);
												if( $del_cust->deleted ){
													unset($_GET['action']);
													unset($_GET['customer']);
													?> 
														<script type="text/javascript">
															<?php if( !empty( $cust->description ) ) { ?>
															var del_message = "<br><strong>Customer "<?php echo ' + '.'"'.$cust->description.'"'; ?> + ' deleted successfully.</strong><br><br>';
															<?php }else{ ?>
																var del_message = 'Customer successfully deleted!';
															<?php } ?>
															jQuery(function() {
																
																jQuery('#message').html(del_message);
																jQuery('#message').fadeIn("slow").show();
															})
														</script>
													<?php
												}
											}
										}

									}	
								}
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
	$plugin_uri = plugins_url( 'processing/create_customer.php' , __FILE__ );
		?>

			<style>
				fieldset {
					border: 1px solid #000000;
					width: 250px;
					padding-left: 20px;
					padding-bottom: 15px;
					-webkit-border-radius: 8px;
					-moz-border-radius: 8px;
					border-radius: 8px;
				}
				
				legend {
					color: #000000;
					padding: 2px;
				}
				
				.legend-text {
					font-weight: italic;
				}
				
				.FormInfo td {
					padding-top: 10px;
					padding-left: 15px;
				}
				
				.FormInfo th {
					padding-top: 10px;
					text-align: right;
				}
			</style>
			<div class='wrap'>
				<h2>Create Customer</h2>
				<div id="message" class="updated" style="display:none;"></div>
				<form action="javascript:void(0);">
					<div style="padding: 10px;">
						<fieldset style="width: 350px;">
							<legend>Personal Information</legend>
							<table class="FormInfo" >
								<tr>	
									<th>Name</th>
									<td>
										<input type="text" 
										   name="customer_name"
										   id="customer_name" 
										   size="35" >
									</td>
								</tr>
								<tr>
									<th>Email</th>
									<td>
										<input type="text" 
										   	   name="customer_email"
										       id="customer_email" 
										       size="35" >
									</td>
								</tr>
								
														
							</table>
						</fieldset>
						
						<fieldset style="margin-top: 15px; width: 350px;">
							<legend>Credit Card Information</legend>
							<table class="FormInfo">
								
								<tr>	
									<th>Card Number</th>
									<td>
										<input type="text" 
										   name="customer_card_number"
										   id="customer_card_number" 
										   size="25" >
									</td>
								</tr>
								<tr>
									<th>Verification<br>Code</th>
									<td>
										<input type="text"
											   name="customer_card_verification_code"
											   id="customer_card_verification_code" 
											   size="25" >
									</td>
								</tr>
								
								<tr>
									<th>Expiration<br>Date</th>
									<td>
										<select name="customer_card_exp_month"
												id="customer_card_exp_month"
												style="width: 50px;">
											<option>01</option>
											<option>02</option>
											<option>03</option>
											<option>04</option>
											<option>05</option>
											<option>06</option>
											<option>07</option>
											<option>08</option>
											<option>09</option>
											<option>10</option>
											<option>11</option>
											<option>12</option>
										</select>
										
										<?php $curr_year = date('Y'); ?>
										<select name="customer_card_exp_year"
												id="customer_card_exp_year"
												style="width: 60px;">
											<?php
												for ($i = 0; $i <= 10; $i++) {
												    echo '<option>'.( $curr_year + $i ).'</option>';
												}											
											?>
										</select>
									</td>
								</tr>
								
								<tr><th></th><td></td></tr>
								<tr>
									<td colspan="2" style="text-align: left;">*Change expiration date to modify</td>
								</tr>
							
							</table>
						</fieldset>
						
						<br>
						
						<input type="button" value="Back" onclick="history.back()">
						
						<input class="submit-button"
							   style="margin-left: 221px;"
							   type="submit"
							   value="Create Customer" >
						
					</div>
				</form>	
			</div>
			<script>
				jQuery(function() {
					jQuery(".submit-button").click(function(){
						var name = jQuery("#customer_name").val();
						var email = jQuery("#customer_email").val();
						var card_number = jQuery("#customer_card_number").val();
						var code = jQuery("#customer_card_verification_code").val();
						var exp_month = jQuery("#customer_card_exp_month").val();
						var exp_year = jQuery("#customer_card_exp_year").val();
						jQuery.get('<?php echo $plugin_uri; ?>', {
							name:jQuery("#customer_name").val(),
							email:jQuery("#customer_email").val(),
							card_number:jQuery("#customer_card_number").val(),
							code:jQuery("#customer_card_verification_code").val(),
							exp_month:jQuery("#customer_card_exp_month").val(),
							exp_year:jQuery("#customer_card_exp_year").val()},
							
							function(data){
								jQuery('#message').html(jQuery.trim(data));
								jQuery('#message').fadeIn("slow").show();
							}
						);
						
					});
				});
			
			
			
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