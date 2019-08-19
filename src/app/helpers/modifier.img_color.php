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
 * In some special cases the requested color may not be returned
 *
 * Library https://github.com/marijnvdwerf/material-palette-php is used.
 */
function smarty_modifier_img_color($url,$variant = "vibrant"){
	$pupiq = new Pupiq($url);
	$colors = $pupiq->getColors();
	return isset($colors[$variant]) ? $colors[$variant] : "";
}
