<?php
class PupiqUtils {
	
	/**
	 * $params = PupiqUtils::DecodeParams('alt=Image,title="Very nice image",data-clickable'); // ["alt" => "Image", "title" => "Very nice image", "data-clickable" => true]
	 */
	static function DecodeParams($params){
		static $placeholder;
		if(!$placeholder){ $placeholder = "~X".uniqid()."X~"; }

		if(!$params){ return array(); }
		if(is_array($params)){ return $params; }

		$out = array();

		$params = strtr($params,array(
			"\\," => "$placeholder-cm",
			"\\=" => "$placeholder-eq",
		));
		$_attrs = preg_split("/,/",trim($params));

		array_walk($_attrs, function($v,$k) use (&$out) {
			$ary = preg_split('/=/',$v);
			if(sizeof($ary)==1){
				$k = trim($ary[0]);
				$out[$k] = true;
			}else{
				// e.g. alt=image,title='Photography + passion = success'
				$k = array_shift($ary);
				$v = join('=',$ary);
				$k = trim($k);
				$v = trim($v);
				$v = strtr($v,array(
					"$placeholder-cm" => ",",
					"$placeholder-eq" => "=",
				));
				# remove quotes from value
				if (preg_match("/^[\"'](.*)[\"']$/", $v, $m)) {
					$v = trim($m[1]);
				}
				$out[$k] = $v;
			}
		});

		return $out;
	}
}
