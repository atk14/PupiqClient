<?php
require_once(__DIR__ . "/../src/app/helpers/modifier.img_url.php");
class TcHelpers extends TcBase {

	function test_modifier_image_url(){
		$image_url = "http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x537_52fa2ef3361053ff.jpg";

		$url1 = smarty_modifier_img_url($image_url);
		$url2 = smarty_modifier_img_url($image_url,"50x50");

		$this->assertTrue(!!strlen($url1));
		$this->assertTrue(!!strlen($url2));

		$this->assertNotEquals($url1,$url2);

		$this->assertContains("_800x537_",$url1);
		$this->assertContains("_50x33_",$url2);
	}
}
