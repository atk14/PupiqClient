<?
/**
 * 
 * <img src="{$url|img_url:"100"}" />
 *
 * Stanoveni vysky:
 * {$url|img_url:"x81"}
 */
function smarty_modifier_img_url($url,$geometry){
	if(is_object($url)){ $url = $url->getUrl(); }
	if(!$url){ return ""; }
	$pupiq = new Pupiq($url);
	$options = array();
	if($class){ $options["attrs"] = array("class" => "selected"); }
	return $pupiq->getUrl($geometry,$options);
}
