<?php
/**
 * Asynchronous version of PupiqAttachmentField offering chunked file upload
 *
 * In order to use AsyncPupiqAttachmentField, the package atk14/async-file-field needs to be installed in the project.
 */
class AsyncPupiqAttachmentField extends AsyncFileField {

	function __construct($options = array()){
		$options += array(
			"required" => true,
		);
		$options += array(
			"widget" => new AsyncPupiqAttachmentInput(array("removal_enabled" => !$options["required"])),
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

		$this->widget->just_created_attachment = $attachment;

		return array(null,$attachment);
	}
}
