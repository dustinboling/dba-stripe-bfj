<?php

class Customer{

	public $id;
	public $name;
	public $email;
	public $delinquent;
	public $created;
	public $c_object;
	public $account_balance;
	public $subscription;
	public $livemode;
	public $discount;
	public $active_card;
		
	public function __construct( $stripe_customer ){
		$cust = json_decode( $stripe_customer );
		$this->id = $cust->id;
		$this->name = $cust->description;
		$this->email = $cust->email;
		$this->delinquent = $cust->delinquent;
		$this->created = $cust->created;
		$this->account_balance = $cust->account_balance;
		$this->subscription = $cust->subscription;
		$this->livemode = $cust->livemode;
		$this->discount = $cust->discount;
		$this->active_card = $cust->active_card;
		$this->c_object = 'customer';
	}

}


