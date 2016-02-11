<?php
class PupiqAttachmentInput extends FileInput{

	/**
	 *
	 */
	function render($name, $value, $options=array()){
		global $HTTP_REQUEST;
		$out = parent::render($name, "", $options);
		$n = "_{$name}_initial_";
		$checkbox_remove = "_{$name}_remove_";
		$url = ($value && (is_string($value) || is_a($value,"PupiqAttachment") || is_a($value,"String"))) ? (string)$value : PupiqAttachmentInput::_UnpackValue($HTTP_REQUEST->getPostVar($n));

		if(!$url){ return $out; }

		$p = new PupiqAttachment($url);
		$image_url = $p->getUrl();
		$out = '<br><a href="'.$image_url.'" class="xpull-left" title="'._('Download attachment').'">'.h($p->getFileName()).'</a> (<input type="checkbox" name="'.$checkbox_remove.'"> '._('remove').')<br>'.$out;
		$out .= '<input type="hidden" name="'.$n.'" value="'.PupiqAttachmentInput::_PackValue($url).'">';

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
			$out = PupiqAttachmentInput::_UnpackValue($HTTP_REQUEST->getPostVar("_{$name}_initial_"));
		}

		if($HTTP_REQUEST->getPostVar("_{$name}_remove_")){
			$out = null;
		}

		return $out;
	}

	protected static function _PackValue($url){
		return Packer::Pack(array("silly_check" => "PupiqAttachmentInput", "url" => (string)$url)); // tady se snazim o jakesi bezpecnejsi zakodovani initial hodnoty
	}

	protected static function _UnpackValue($packed_val){
		if(Packer::Unpack($packed_val,$v) && isset($v["silly_check"]) && $v["silly_check"]=="PupiqAttachmentInput" && isset($v["url"])){
			return $v["url"];
		}
	}
}
