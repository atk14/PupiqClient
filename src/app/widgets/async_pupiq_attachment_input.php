<?php
class AsyncPupiqAttachmentInput extends AsyncFileInput {

	var $removal_enabled;
	var $just_created_attachment;

	function __construct($options = array()){
		$options += array(
			"removal_enabled" => true,
		);

		$this->removal_enabled = $options["removal_enabled"];
		unset($options["removal_enabled"]);

		parent::__construct($options);
	}

	/**
	 *
	 */
	function render($name, $value, $options=array()){
		global $HTTP_REQUEST;
		$out = parent::render($name, "", $options);
		$n = "_{$name}_initial_";
		$checkbox_remove = "_{$name}_remove_";

		if($this->just_created_attachment){
			$value = (string)$this->just_created_attachment;
		}

		$url = ($value && (is_string($value) || is_a($value,"PupiqAttachment") || is_a($value,"String"))) ? (string)$value : AsyncPupiqAttachmentInput::_UnpackValue($HTTP_REQUEST->getPostVar($n));

		if($HTTP_REQUEST->getPostVar($checkbox_remove)){
			$url = null;
		}

		if(!$url){ return $out; }

		$p = new PupiqAttachment($url);
		$image_url = $p->getUrl();
		$removal_chekbox = $this->removal_enabled ? ' <span class="file_removal_checkbox"><input type="checkbox" name="'.$checkbox_remove.'"> '._('remove').'</span>' : '';
		$out = '<div class="form-control-wrap"><a href="'.$image_url.'" title="'._('Download attachment').'">'.h($p->getFileName()).'</a>'.$removal_chekbox.$out.'</div>';
		$out .= '<input type="hidden" name="'.$n.'" value="'.AsyncPupiqAttachmentInput::_PackValue($url).'">';

		return $out;
	}

	/**
	 *
	 * !! Pozor !! Vracena je instance HTTPUploadedFile nebo string (initial hodnota)
	 */
	function value_from_datadict($data,$name){
		global $HTTP_REQUEST;
		$out = parent::value_from_datadict($data,$name);
		if(!$out){
			$out = AsyncPupiqAttachmentInput::_UnpackValue($HTTP_REQUEST->getPostVar("_{$name}_initial_"));
		}

		if($this->removal_enabled && $HTTP_REQUEST->getPostVar("_{$name}_remove_")){
			$out = null;
		}

		return $out;
	}

	protected static function _PackValue($url){
		return Packer::Pack(array("silly_check" => "AsyncPupiqAttachmentInput", "url" => (string)$url)); // tady se snazim o jakesi bezpecnejsi zakodovani initial hodnoty
	}

	protected static function _UnpackValue($packed_val){
		if(Packer::Unpack($packed_val,$v) && isset($v["silly_check"]) && $v["silly_check"]=="AsyncPupiqAttachmentInput" && isset($v["url"])){
			return $v["url"];
		}
	}
}
