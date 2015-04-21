<?php
class PupiqAttachmentField extends FileField{
	function __construct($options = array()){
		parent::__construct($options);
	}

	function clean($value){
		list($err,$value) = parent::clean($value);
		if($err || !$value){ return array($err,$value); }

		if(!$attachment = Pupiq::CreateAttachment($value->getTmpFileName(),$value->getFileName(),$err_msg)){
			return array($err_msg,null);
		}

		return array(null,$attachment);
	}
}
