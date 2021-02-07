<?php
/**
 * Determines the dominant color of the given image
 * 
 * Possible variants:
 *
 * - vibrant
 * - light_vibrant
 * - dark_vibrant
 * - muted
 * - light_muted
 * - dark_muted
 *
 * Usage:
 *
 *	{$image_url|img_color:"light_vibrant"|default:"#FFFFFF"}
 *
 * In some cases the requested color may not be detected in the image.
 * So it may be useful to specify multiple colors in the desired order:
 *
 *	{$image_url|img_color:"light_vibrant or light_muted or muted"}
 *
 * Library https://github.com/marijnvdwerf/material-palette-php is used.
 */
function smarty_modifier_img_color($url,$variant = "vibrant"){
	$pupiq = new Pupiq($url);
	$colors = $pupiq->getColors();

	$variant = trim($variant);
	$variant = strtolower($variant);
	$variant_ar = preg_split('/\s+(or\s+|)/',$variant); // 

	foreach($variant_ar as $v){
		if(isset($colors[$v])){ return $colors[$v]; }
	}

	return "";
}
