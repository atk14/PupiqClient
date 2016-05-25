<?php
/**
 * Prints out an url of the given image
 * 
 * <img src="{$url|img_url:"100"}" />
 *
 * You can set a transformation string as the second parameter:
 * {$url|img_url:"x81"}
 *
 * More info at https://github.com/atk14/PupiqClient
 */
function smarty_modifier_img_url($url,$geometry){
	if(is_object($url)){ $url = $url->getUrl(); }
	if(!$url){ return ""; }
	$pupiq = new Pupiq($url);
	$options = array();
	if($class){ $options["attrs"] = array("class" => "selected"); }
	return $pupiq->getUrl($geometry,$options);
}
