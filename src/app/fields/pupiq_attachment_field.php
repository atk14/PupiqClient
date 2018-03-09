<?php
class PupiqAttachmentField extends FileField{

	function __construct($options = array()){
		$options += array(
			"widget" => new PupiqAttachmentInput(), 
		);
		parent::__construct($options);
	}

	function clean($value){
		if(is_string($value)){
			return array(null,new PupiqAttachment($value));
		}

		list($err,$file) = parent::clean($value);
		if($err || !$file){ return array($err,$file); }

		$attachment = Pupiq::CreateAttachment($file->getTmpFileName(),$file->getFileName(),$err_msg);
		//$file->cleanUp(); // unlink temporary file

		if(!$attachment){
			return array($err_msg,null);
		}

		return array(null,$attachment);
	}
}
