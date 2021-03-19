<?php
/**
 * Cleaned value is a Pupiq instance.
 */
class PupiqImageField extends ImageField{

	function __construct($options = array()){
		$options += array(
			"required" => true,
		);
		$options += array(
			"widget" => new PupiqImageInput(array("removal_enabled" => !$options["required"])),
		);
		parent::__construct($options);
	}

	function clean($value){
		if(is_string($value)){
			return array(null,new Pupiq($value));
		}

		list($err,$file) = parent::clean($value);
		if($err || !$file){ return array($err,$file); }

		$pupiq = Pupiq::CreateImage(array(
			"path" => $file->getTmpFileName(),
			"name" => $file->getFileName(),
		),$err_msg);
		//$file->cleanUp(); // unlink temporary file

		if(!$pupiq){
			return array($err_msg,null);
		}

		$this->widget->just_created_image = $pupiq;

		return array(null,$pupiq);
	}
}
