<?php
/**
 * Cleaned value is a Pupiq instance.
 */
class PupiqImageField extends ImageField{
	function __construct($options = array()){
		$options += array(
			"widget" => new PupiqImageInput(),
		);
		parent::__construct($options);
	}

	function clean($value){
		if(is_string($value)){
			return array(null,new Pupiq($value));
		}

		list($err,$value) = parent::clean($value);

		if($err || !$value){ return array($err,$value); }

		if(!$pupiq = Pupiq::CreateImage($value->getTmpFileName(),$err_msg)){
			return array($err_msg,null);
		}

		return array(null,$pupiq);
	}
}
