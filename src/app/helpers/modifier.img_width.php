<?php
/**
 * Calculates the image width by the given geometry string
 *
 * Usage in a Smarty template:
 * {$image_url|img_width:"800"}
 */
function smarty_modifier_img_width($url,$geometry = ""){
	if(is_object($url)){ $url = $url->getUrl(); }
	$pupiq = new Pupiq($url);
	$pupiq->setGeometry($geometry);
	return $pupiq->getWidth();
}
