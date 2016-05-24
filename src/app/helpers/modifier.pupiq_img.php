<?php
/**
 * Smarty modifier to render img tag using image from Pupiq
 * More info na https://secure.orbit41.com:441/en/wiki/pupiq/
 * @package Pupiq
 * @package Helpers
 */

/**
 * Smarty modifier to render img tag using image from Pupiq
 * More info at https://secure.orbit41.com:441/en/wiki/pupiq/
 *
 * @param string $url
 * @param string $geometry
 * @param ApiUser $user
 * @param string $attrs
 */
function smarty_modifier_pupiq_img($url,$geometry = "100",$user = null,$attrs=null){
	if(!$url){ return; }
	if(is_object($url)){
		$url = $url->getUrl();
	}
	$api_key = isset($user) ? $user->getApiKey() : null;
	$attrsAr = array();

	# muzeme poslat dalsi atributy jako retezec => "class='image image-warning'|title='Warning sign'"
	if ($attrs=trim($attrs)) {
		if (is_string($attrs)) {
			$_attrs = preg_split("/,/",$attrs);
			array_walk($_attrs, function($v,$k) use (&$attrsAr) {
				list($k,$v) = preg_split("/=/", $v);
				# remove quotes from value
				if (preg_match("/^[\"'](.+)[\"']$/", $v, $m)) {
					$v = $m[1];
				}
				$attrsAr[$k]=$v;
			});
		}
	}
	$p = new Pupiq($url,$api_key);
	return $p->getImgTag($geometry, array("attrs" => $attrsAr));
}
