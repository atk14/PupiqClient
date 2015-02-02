<?php
/**
 * Vice info na https://secure.orbit41.com:441/en/wiki/pupiq/
 */
function smarty_modifier_pupiq_img($url,$geometry = "100",$user = null){
	if(!$url){ return; }
	if(is_object($url)){
		$url = $url->getUrl();
	}
	$api_key = isset($user) ? $user->getApiKey() : null;
	$p = new Pupiq($url,$api_key);
	return $p->getImgTag($geometry);
}
