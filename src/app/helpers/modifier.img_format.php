<?php
function smarty_modifier_img_format($url,$geometry = ""){
	if(is_object($url)){ $url = $url->getUrl(); }
	$pupiq = new Pupiq($url);
	if($geometry){
		$pupiq->setGeometry($geometry);
	}
	return $pupiq->getFormat();
}
