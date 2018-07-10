<?php
class PupiqImageInput extends FileInput{

	var $removal_enabled;

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
		$url = ($value && (is_string($value) || is_a($value,"Pupiq") || is_a($value,"String"))) ? (string)$value : PupiqImageInput::_UnpackValue($HTTP_REQUEST->getPostVar($n));

		if(!$url){ return $out; }

		$p = new Pupiq($url);
		$width = $p->getOriginalWidth();
		$height = $p->getOriginalHeight();
		$geom = ($width>800 || $height>800 || !$width || !$height) ? "800x800" : "{$width}x$height";
		$image_url = $p->getUrl($geom);
		$image_tag = $p->getImgTag("!100x100",array("attrs" => array("class" => "img-thumbnail", "style" => "margin-right: 12px;")));
		$removal_chekbox = $this->removal_enabled ? '<br><input type="checkbox" name="'.$checkbox_remove.'"> '._('remove').')' : '';
		$out = '<div class="clearfix"><a href="'.$image_url.'" class="pull-left" title="'._('Display image').'">'.$image_tag.'</a>'.$out.$removal_chekbox.'</div>'; //'<div style="clear: both;"></div>';
		$out .= '<input type="hidden" name="'.$n.'" value="'.PupiqImageInput::_PackValue($url).'">';

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
			$out = PupiqImageInput::_UnpackValue($HTTP_REQUEST->getPostVar("_{$name}_initial_"));
		}

		if($this->removal_enabled && $HTTP_REQUEST->getPostVar("_{$name}_remove_")){
			$out = null;
		}

		return $out;
	}

	protected static function _PackValue($url){
		return Packer::Pack(array("silly_check" => "PupiqImageInput", "url" => (string)$url)); // tady se snazim o jakesi bezpecnejsi zakodovani initial hodnoty
	}

	protected static function _UnpackValue($packed_val){
		if(Packer::Unpack($packed_val,$v) && isset($v["silly_check"]) && $v["silly_check"]=="PupiqImageInput" && isset($v["url"])){
			return $v["url"];
		}
	}
}
