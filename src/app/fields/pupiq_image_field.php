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

		list($err,$file) = parent::clean($value);
		if($err || !$file){ return array($err,$file); }

		$pupiq = Pupiq::CreateImage($file->getTmpFileName(),$err_msg)
		$file->cleanUp(); // unlink temporary file

		if(!$pupiq){
			return array($err_msg,null);
		}

		return array(null,$pupiq);
	}
}
