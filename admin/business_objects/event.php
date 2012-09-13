<?php

class Event{

	public $id;
	public $e_object;
	public $livemode;
	public $created;
	public $type;
	public $charge_id;
	
	public function __construct( $stripe_event ){
		$event = json_decode( $stripe_event );
		
		$this->id = $event->id;
		$this->e_object = 'event';
		$this->livemode = $event->livemode;
		$this->created = $event->created;
		$this->type = $event->type;
		
		$tmp_data = $event->data;
		$this->charge_id = $tmp_data->object->id;
	}
}


