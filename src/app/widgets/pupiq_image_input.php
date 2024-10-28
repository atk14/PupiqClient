<?php
class PupiqImageInput extends FileInput{

	var $removal_enabled;
	var $just_created_image; // Pupiq

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

		if($this->just_created_image){
			$value = (string)$this->just_created_image;
		}

		$url = ($value && (is_string($value) || is_a($value,"Pupiq") || is_a($value,"String"))) ? (string)$value : PupiqImageInput::_UnpackValue($HTTP_REQUEST->getPostVar($n));

		if($HTTP_REQUEST->getPostVar($checkbox_remove)){
			$url = null;
		}

		$background_pattern = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgAQMAAABJtOi3AAAAGXRFWHRDb21tZW50AENyZWF0ZWQgd2l0aCBHSU1QV4EOFwAAAAlwSFlzAAAOdQAADnUBuWNRMgAAAAZQTFRF29vb4+PjNRpY5wAAABRJREFUCNdjYPgPhEQQRCpjoLJ5AO29P8G2eCQbAAAAAElFTkSuQmCC";
		$no_image_icon = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABkBAMAAACCzIhnAAAACXBIWXMAAAsTAAALEwEAmpwYAAANeGlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iWE1QIENvcmUgNC40LjAtRXhpdjIiPgogPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iCiAgICB4bWxuczpzdEV2dD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlRXZlbnQjIgogICAgeG1sbnM6ZGM9Imh0dHA6Ly9wdXJsLm9yZy9kYy9lbGVtZW50cy8xLjEvIgogICAgeG1sbnM6R0lNUD0iaHR0cDovL3d3dy5naW1wLm9yZy94bXAvIgogICAgeG1sbnM6dGlmZj0iaHR0cDovL25zLmFkb2JlLmNvbS90aWZmLzEuMC8iCiAgICB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iCiAgIHhtcE1NOkRvY3VtZW50SUQ9ImdpbXA6ZG9jaWQ6Z2ltcDo2NmY2ZTk1MC1iYTIzLTQ5OGYtOWUxMi01NzAzY2YzYWFlNmMiCiAgIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6OGRiOTRkZTctMGNmMS00MDVkLWE1ZTYtY2M3ODBhZGVmYzg2IgogICB4bXBNTTpPcmlnaW5hbERvY3VtZW50SUQ9InhtcC5kaWQ6YWMxMTg5MGEtZWU3NC00NzAzLWJhZDYtMDY4MjVhYzg3MWYwIgogICBkYzpGb3JtYXQ9ImltYWdlL3BuZyIKICAgR0lNUDpBUEk9IjIuMCIKICAgR0lNUDpQbGF0Zm9ybT0iTGludXgiCiAgIEdJTVA6VGltZVN0YW1wPSIxNzMwMTEwMzcyNzc2MTU2IgogICBHSU1QOlZlcnNpb249IjIuMTAuMzYiCiAgIHRpZmY6T3JpZW50YXRpb249IjEiCiAgIHhtcDpDcmVhdG9yVG9vbD0iR0lNUCAyLjEwIgogICB4bXA6TWV0YWRhdGFEYXRlPSIyMDI0OjEwOjI4VDExOjEyOjUyKzAxOjAwIgogICB4bXA6TW9kaWZ5RGF0ZT0iMjAyNDoxMDoyOFQxMToxMjo1MiswMTowMCI+CiAgIDx4bXBNTTpIaXN0b3J5PgogICAgPHJkZjpTZXE+CiAgICAgPHJkZjpsaQogICAgICBzdEV2dDphY3Rpb249InNhdmVkIgogICAgICBzdEV2dDpjaGFuZ2VkPSIvIgogICAgICBzdEV2dDppbnN0YW5jZUlEPSJ4bXAuaWlkOmJiN2M4OGJiLWQ5NjktNDIzNi05Njg0LWZjYzEyM2M4NjA2YiIKICAgICAgc3RFdnQ6c29mdHdhcmVBZ2VudD0iR2ltcCAyLjEwIChMaW51eCkiCiAgICAgIHN0RXZ0OndoZW49IjIwMjQtMTAtMjhUMTE6MTI6NTIrMDE6MDAiLz4KICAgIDwvcmRmOlNlcT4KICAgPC94bXBNTTpIaXN0b3J5PgogIDwvcmRmOkRlc2NyaXB0aW9uPgogPC9yZGY6UkRGPgo8L3g6eG1wbWV0YT4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgIAo8P3hwYWNrZXQgZW5kPSJ3Ij8+UBvr1QAAAMR6VFh0UmF3IHByb2ZpbGUgdHlwZSBleGlmAAB42m1QwQ3DIAz8M0VHwD4DZhzSpFI36Pg14ERJlEMcxmcdxmH7fT/h1cEkQVLRXHOOBqlSuVmgcaINpiiDB7K4Rtd8OAS2FOzEvGr2+j1Ph8E8mkXpZKRvF5arUP0B1puRP4TeEVuwulF1I/AUyA1a869ULecvLFu8QucOnVCG92Fyv0ux6a3JkmDeQIjGQJ4NoG8JaF0wZqgVkq2eYeME9k5sIE9z2hH+9E9ZIlvziUAAAAAqUExURfv7+/r6+t3d3dzc3PLy8tvb2/Pz8/n5+d7e3vHx8eXl5ebm5urq6uvr6xYCRoAAAAF7SURBVFjD7ZZBboMwEEVtCbG2pUhRm50pUtaWvYe6J8DKvqvuc4VcITdob9A79AS9UMcEKAaEZ/b+Ulbx0+fb+A+MZWVlZWVt6YNMFFrs/i+XYsx1uwi/qVhnUViza8JrHRFVJ5xv9pHqGj3WUR8SJowrESG8uidMAJHzNGdZp0wCMktTdbJOmQRklgaRpEeUmG1GMskKSSdZI74lInDwJ0FAynfmfPtJQZwBE64ISGH9r29JiPP2Yk4SiUCKkPwHkmARZ4LJmzVHjUQO1jdgAtv11eEQeEnCejDhNRKpAxB+Ddx6JBIeK5iEbkEisPb1pb/x8euzkyWsfdwTLNIvetQKBRm6i4IM3UVAxoJEx79KOPihLpCbrJU1z2MpoZBbdfHt91h9GETIJ0gyFSwKYfOqxyHRPMEh0TxBIXELY5BFC2OQRQujkOHgp3GRRIrx4P/Hawpx08FPQzxtwlafCimT5NDaMCGKblLSTUpFNYEvq/y5mpWVlbWpP1h6XN/fRjEXAAAAAElFTkSuQmCC";

		if(!$url){
			$image_tag = '<img src="'.$no_image_icon.'" width="100" height="100" class="img-thumbnailx" style="border: none; margin-right: 12px; background-image: url(\''.$background_pattern.'"\');">';
			$image_tag .= '
				<br>
				<small style="display: block; width: 100px; background-color: grey; color: white; text-align: center; text-decoration: none;">'.
					_("no image").
				'</small>
			';
			$out = '<div class="form-control-wrap"><div class="pull-left">'.$image_tag.'</div>'.$out.'</div>'; //'<div style="clear: both;"></div>';
			return $out;
		}

		$p = new Pupiq($url);
		$width = $p->getOriginalWidth();
		$height = $p->getOriginalHeight();
		$geom = ($width>800 || $height>800 || !$width || !$height) ? "800x800" : "{$width}x$height";
		$image_url = $p->getUrl($geom);
		$image_tag = $p->getImgTag("100x100xtransparent",array("attrs" => array("class" => "img-thumbnail", "style" => "margin-right: 12px; background-image: url('".$background_pattern."');")));
		$image_tag .= '
			<br>
			<small style="display: block; width: 100px; background-color: grey; color: white; text-align: center; text-decoration: none;">'.
				sprintf("%sx%s %s",$p->getOriginalWidth(),$p->getOriginalHeight(),$p->getFormat()).
			'</small>
		';
		$removal_chekbox = $this->removal_enabled ? ' <span class="file_removal_checkbox"><input type="checkbox" name="'.$checkbox_remove.'"> '._('remove').'</span>' : '';
		$out = '<div class="form-control-wrap"><a href="'.$image_url.'" class="pull-left" title="'._('Display image').'">'.$image_tag.'</a>'.$removal_chekbox.$out.'</div>'; //'<div style="clear: both;"></div>';
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
