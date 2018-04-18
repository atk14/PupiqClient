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

	// Other parameters can be passed as an formatted string, e.g. "class='image image-warning',title='Warning sign',"
	$attrsAr = PupiqUtils::DecodeParams($attrs);

	$p = new Pupiq($url);
	return $p->getImgTag($geometry, array("attrs" => $attrsAr));
}
