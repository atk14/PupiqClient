<?php
/**
 * Calculates the image height by the given geometry string
 *
 * Usage in a Smarty template:
 * {$image_url|img_height:"800"}
 */
function smarty_modifier_img_height($url,$geometry = ""){
	if(is_object($url)){ $url = $url->getUrl(); }
	$pupiq = new Pupiq($url);
	$pupiq->setGeometry($geometry);
	return $pupiq->getHeight();
}
