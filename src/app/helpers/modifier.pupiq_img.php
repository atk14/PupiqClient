<?php
/**
 * Smarty modifier to render img tag using image from Pupiq
 * More info at https://github.com/atk14/PupiqClient
 *
 * @package Pupiq
 * @package Helpers
 */

/**
 * Smarty modifier to render img tag using image from Pupiq
 * More info at https://github.com/atk14/PupiqClient#usage-in-templates
 *
 * @param string $url
 * @param string $geometry parameters for Pupiq service with information what to do with the image
 * @param string $attrs additional attributes for img tag
 */
function smarty_modifier_pupiq_img($url,$geometry = "100",$attrs=null){
	if(!$url){ return; }
	if(is_object($url)){
		$url = $url->getUrl();
	}

	# muzeme poslat dalsi atributy jako retezec => "class='image image-warning',title='Warning sign'"
	# we can pass 
	$attrsAr = array();
	if ($attrs) {
		if (is_string($attrs)) {
			$_attrs = preg_split("/,/",trim($attrs));
			array_walk($_attrs, function($v,$k) use (&$attrsAr) {
				list($k,$v) = preg_split("/=/", $v);
				$k = trim($k);
				$v = trim($v);
				# remove quotes from value
				if (preg_match("/^[\"'](.+)[\"']$/", $v, $m)) {
					$v = trim($m[1]);
				}
				$attrsAr[$k]=$v;
			});
		} else {
			$attrsAr = $attrs;
		}
	}
	$p = new Pupiq($url);
	return $p->getImgTag($geometry, array("attrs" => $attrsAr));
}
