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
		$file_input = parent::render($name, "", $options);
		$n = "_{$name}_initial_";
		$checkbox_remove = "_{$name}_remove_";

		if($this->just_created_image){
			$value = (string)$this->just_created_image;
		}

		$url = ($value && (is_string($value) || is_a($value,"Pupiq") || is_a($value,"String"))) ? (string)$value : PupiqImageInput::_UnpackValue($HTTP_REQUEST->getPostVar($n));

		if($HTTP_REQUEST->getPostVar($checkbox_remove)){
			$url = null;
		}

		$pupiq_server_url = preg_replace('/\/api\/$/','',PUPIQ_API_URL); // "https://i.pupiq.net/api/" -> "https://i.pupiq.net"

		$pupiq = new Pupiq();
		$auth_token = $pupiq->getAuthToken(array("salt" => "media_gallery"));
		$image_library_url = "$pupiq_server_url/media_gallery/".PUPIQ_LANG."/images/?auth_token=".urlencode($auth_token)."&origin=".urlencode($HTTP_REQUEST->getServerUrl())."&field_name=".urlencode($name);
		$js = str_replace("\n"," ",trim('
			var win = window.open("'.$image_library_url.'","pupiq_image_library","width=800,height=600,toolbar=no");
			win.focus();
		'));
		$image_library_snippet = '<a href="#" onclick="javascript: '.h($js).' return false;">'._('image library').'</a><br>';

		//if(!$url){ return $file_input; }

		$out = "";

		$out .= '
			<script>
			//<![CDATA[
			window.addEventListener("message", function(event){
				if (event.origin !== "'.$pupiq_server_url.'") {
					return;
				}
				// console.log(event.data);
				var data = event.data;
				var $field = $("input[name="+data.field_name+"]");
				var $thumbnail_img = $field.parent("div[class=form-control-wrap]").find("img[class=img-thumbnail]");
				
				$thumbnail_img.attr("src",data.image.thumbnail);
				$thumbnail_img.parent("a").attr("href",data.image.detail);
				$("input[name=_"+data.field_name+"_initial_]").val(data.image.url);
			});
			//]]>
			</script>
		';

		$removal_chekbox = "";
		$image_url = "";

		if($url){
			$p = new Pupiq($url);
			$width = $p->getOriginalWidth();
			$height = $p->getOriginalHeight();
			$geom = "800x800";
			$image_url = $p->getUrl($geom);
			$thumbnail_image_tag = $p->getImgTag("100x100xffffff",array("attrs" => array("class" => "img-thumbnail", "style" => "margin-right: 12px;")));
			$thumbnail = '<a href="'.$image_url.'" class="pull-left float-left" title="'._('Display image').'">'.$thumbnail_image_tag.'</a>';
			if($this->removal_enabled){
				$removal_chekbox = ' <input type="checkbox" name="'.$checkbox_remove.'"> '._('remove')."<br>";
			}
		}else{
			$thumbnail_image_tag = '<img src="'.$pupiq_server_url.'/public/images/camera.svg" width="100" height="100" alt="" class="img-thumbnail" style="margin-right: 12px;">';
			$thumbnail = '<a class="pull-left float-left" title="'._('Display image').'">'.$thumbnail_image_tag.'</a>';
		}

		$out .= '<div class="form-control-wrap">'.$thumbnail.'<div class="pull-left float-left">'.$image_library_snippet.$removal_chekbox.'</div>'.$file_input.'</div>'; //'<div style="clear: both;"></div>';
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
		if(!$url){ return ""; }
		return Packer::Pack(array("silly_check" => "PupiqImageInput", "url" => (string)$url)); // tady se snazim o jakesi bezpecnejsi zakodovani initial hodnoty
	}

	protected static function _UnpackValue($packed_val){
		if(!$packed_val){ return null; }
		if(preg_match('/^https?:\/\//',$packed_val)){
			// TODO: Validate the image URL through Pupiq API ("images/detail")
			return $packed_val;
		}
		if(Packer::Unpack($packed_val,$v) && isset($v["silly_check"]) && $v["silly_check"]=="PupiqImageInput" && isset($v["url"])){
			return $v["url"];
		}
	}
}
