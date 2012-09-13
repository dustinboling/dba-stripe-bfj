<?php

class Charge{

	public $id;
	public $c_object;
	public $livemode;
	public $amount;
	public $proceeds;
	public $card;
	public $created;
	public $currency;
	public $disputed;
	public $fee;
	public $fee_details;
	public $paid;
	public $refunded;
	public $amount_refunded;
	public $customer;
	public $description;
	public $failure_message;
	public $invoice;
		
	public function __construct( $stripe_data ){
		if( get_class( $stripe_data ) != 'BasicCharge' ){
			$charge = json_decode( $stripe_data );
			$this->id = $charge->id;
			$this->livemode = $charge->livemode;
			$this->amount = $charge->amount;
			$this->proceeds = $charge->amount - $charge->fee;
			$this->card = $charge->card;
			$this->created = $charge->created;
			$this->currency = $charge->currency;
			$this->disputed = $charge->disputed;
			$this->fee = $charge->fee;
			$this->fee_details = $charge->fee_details;
			$this->paid = $charge->paid;
			$this->refunded = $charge->refunded;
			$this->amount_refunded = $charge->amount_refunded;
			$this->customer = $charge->customer;
			$this->description = $charge->description;
			$this->failure_message = $charge->failure_message;
			$this->invoice = $charge->invoice;
			$this->c_object = 'charge';
		}else{
			$chrg_data = json_decode(Stripe_Charge::retrieve( $stripe_data->id ) );
			$this->id = $stripe_data->id;
			$this->c_object = 'charge';
			$this->fee = $stripe_data->fee;
			$this->created = $stripe_data->created;
			$this->currency = $stripe_data->currency;
			$this->fee_details = $stripe_data->fee_details;
			$this->amount = $stripe_data->amount;
			$this->proceeds = $stripe_data->proceeds;
			$this->livemode = $chrg_data->livemode;
			$this->card = $chrg_data->card;
			$this->disputed = $chrg_data->disputed;
			$this->paid = $chrg_data->paid;
			if( $this->amount < 0 ){
				$this->refunded = true;	
			}else{
				$this->refunded = false;
			}
			$this->amount_refunded = $chrg_data->amount_refunded;
			$this->customer = $chrg_data->customer;
			$this->description = $chrg_data->description;
			$this->failure_message = $chrg_data->failure_message;
			$this->invoice = $chrg_data->invoice;
		}	
	}

}


