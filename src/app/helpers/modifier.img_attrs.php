<?php
/**
 * Prints out some tag's attributes of the given image
 *
 * In a Smarty template:
 * <img {!$image_url|img_attrs:"200x200"} alt="Really cool image">
 *
 * Output: 
 * <img src="http://i.pupiq.net/i/path/to/image" width="200" height="150" alt="Really cool image">
 */
function smarty_modifier_img_attrs($url,$geometry = ""){
	if(is_object($url)){ $url = $url->getUrl(); }
	if(!$url){ return ""; }
	$pupiq = new Pupiq($url);
	$pupiq->setGeometry($geometry);
	return sprintf('src="%s" width="%s" height="%s"',$pupiq->getUrl(),$pupiq->getWidth(),$pupiq->getHeight());
}
