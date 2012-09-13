<?php

class BasicCharge{

	public $id;
	public $bc_object;
	public $fee;
	public $created;
	public $currency;
	public $fee_details;
	public $amount;
	public $proceeds;
	
	public function __construct( $stripe_basic_charge ){
		$charge = json_decode( $stripe_basic_charge );
		$fee_details = $charge->fee_details;
		$fee_details = $fee_details[0];
		
		// Set basic charge data
		$this->id = $charge->id;
		$this->bc_object = 'charge';
		$this->fee = $charge->fee;
		$this->created = $charge->created;
		$this->currency = $fee_details->currency;
		$this->fee_details = $fee_details->description;
		$this->amount = $charge->amount;
		$this->proceeds = $charge->net;
	}

}


