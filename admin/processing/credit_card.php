<?php

class CreditCard{

	private $card_number;
	private $exp_month;
	private $exp_year;
	private $cvc;
	private $address1;
	private $address2;
	private $city;
		
	public function __construct( $card_number, $cvc, $exp_month, $exp_year ){
		$this->card_number = $card_number;
		$this->cvc = $cvc;
		$this->exp_month = $exp_month;
		$this->exp_year = $exp_year;
		
	}

	public function get_card_number(){ return $this->card_number; }
	public function get_cvc(){ return $this->cvc; }
	public function get_exp_month(){ return $this->exp_month; }
	public function get_exp_year(){ return $this->exp_year; }
}


